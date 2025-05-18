<?php
session_start();
require_once 'config.php';

$error_messages = array();

// Get raw input
$imie = trim($_POST['imie'] ?? '');
$nazwisko = trim($_POST['nazwisko'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefon = trim($_POST['telefon'] ?? '');
$login = trim($_POST['login'] ?? '');
$haslo = $_POST['haslo'] ?? '';
$powtorz_haslo = $_POST['powtorz_haslo'] ?? '';
$data_urodzenia = trim($_POST['data_urodzenia'] ?? '');
$kategoria_prawa_jazdy = trim($_POST['kategoria_prawa_jazdy'] ?? '');

// Debug information
error_log("Registration attempt for user: " . $login);
error_log("Email: " . $email);

// Validate required fields
if (empty($imie) || empty($nazwisko) || empty($email) || empty($telefon) || empty($login) || empty($haslo) || empty($powtorz_haslo) || empty($data_urodzenia) || empty($kategoria_prawa_jazdy)) {
    $error_messages[] = "Wszystkie pola są wymagane.";
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_messages[] = "Nieprawidłowy format adresu email.";
}

// Validate phone number format
if (!preg_match("/^[0-9]{9}$/", $telefon)) {
    $error_messages[] = "Numer telefonu musi składać się z 9 cyfr.";
}

// Validate password match
if ($haslo !== $powtorz_haslo) {
    $error_messages[] = "Hasła nie są identyczne.";
}

// Validate password strength
if (strlen($haslo) < 8) {
    $error_messages[] = "Hasło musi mieć co najmniej 8 znaków.";
}

// Validate date of birth
$date_now = new DateTime();
$date_birth = new DateTime($data_urodzenia);
$age = $date_now->diff($date_birth)->y;

if ($age < 16) {
    $error_messages[] = "Musisz mieć co najmniej 16 lat, aby się zarejestrować.";
}

// Check if email already exists
$stmt = $conn->prepare("SELECT 1 FROM uzytkownicy WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $error_messages[] = "Ten adres email jest już zarejestrowany.";
}

// Check if login already exists
$stmt = $conn->prepare("SELECT 1 FROM uzytkownicy WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $error_messages[] = "Ten login jest już zajęty.";
}

// If there are any errors, redirect back to registration form
if (!empty($error_messages)) {
    $_SESSION['error_messages'] = $error_messages;
    header("Location: rejestracja.php");
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Hash password with strong options
    $hashed_password = password_hash($haslo, PASSWORD_DEFAULT, ['cost' => 12]);
    
    // Debug information
    error_log("Password hash generated for user: " . $login);
    error_log("Hash length: " . strlen($hashed_password));

    // Insert user data
    $stmt = $conn->prepare("
        INSERT INTO uzytkownicy (
            imie, nazwisko, email, telefon, login, haslo, 
            data_urodzenia, kategoria_prawa_jazdy, rola, data_rejestracji
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'kursant', NOW())
    ");

    $stmt->bind_param(
        "ssssssss",
        $imie, $nazwisko, $email, $telefon, $login, $hashed_password,
        $data_urodzenia, $kategoria_prawa_jazdy
    );

    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas rejestracji użytkownika: " . $stmt->error);
    }

    // Debug information
    error_log("User registered successfully: " . $login);
    error_log("User ID: " . $conn->insert_id);

    // Commit transaction
    $conn->commit();

    // Set success message and redirect to login page
    $_SESSION['success_message'] = "Rejestracja zakończona pomyślnie. Możesz się teraz zalogować.";
    header("Location: login.php?registration=success");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Registration error for user " . $login . ": " . $e->getMessage());
    $_SESSION['error_messages'] = array("Wystąpił błąd podczas rejestracji: " . $e->getMessage());
    header("Location: rejestracja.php");
    exit();
}
?>
