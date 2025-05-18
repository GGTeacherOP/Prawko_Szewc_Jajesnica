<?php
require_once 'config.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $login = sanitize_input($_POST['login']);
    $imie = sanitize_input($_POST['imie']);
    $nazwisko = sanitize_input($_POST['nazwisko']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telefon = sanitize_input($_POST['telefon']);
    $data_urodzenia = $_POST['data_urodzenia'];
    $kategoria_prawa_jazdy = $_POST['kategoria_prawa_jazdy'];
    $haslo = $_POST['haslo'];
    $potwierdz_haslo = $_POST['potwierdz_haslo'];

    // Validate inputs
    $errors = [];

    if (empty($login) || !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $login)) {
        $errors[] = "Login musi zawierać od 3 do 50 znaków i może składać się tylko z liter, cyfr i podkreśleń.";
    }

    if (empty($imie) || empty($nazwisko)) {
        $errors[] = "Imię i nazwisko są wymagane.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Nieprawidłowy format email.";
    }

    if (empty($telefon) || !preg_match('/^[0-9]{9,15}$/', $telefon)) {
        $errors[] = "Nieprawidłowy numer telefonu.";
    }

    if (empty($data_urodzenia)) {
        $errors[] = "Data urodzenia jest wymagana.";
    }

    if (empty($haslo) || strlen($haslo) < 8) {
        $errors[] = "Hasło musi mieć co najmniej 8 znaków.";
    }

    if ($haslo !== $potwierdz_haslo) {
        $errors[] = "Hasła nie są takie same.";
    }

    // Check if login already exists
    $check_login_query = "SELECT * FROM uzytkownicy WHERE login = ?";
    $stmt = $conn->prepare($check_login_query);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Użytkownik o tym loginie już istnieje.";
    }
    $stmt->close();

    // Check if email already exists
    $check_email_query = "SELECT * FROM uzytkownicy WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Użytkownik o tym adresie email już istnieje.";
    }
    $stmt->close();

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $insert_query = "INSERT INTO uzytkownicy (login, imie, nazwisko, email, telefon, data_urodzenia, haslo, kategoria_prawa_jazdy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssssssss", $login, $imie, $nazwisko, $email, $telefon, $data_urodzenia, $hashed_password, $kategoria_prawa_jazdy);

        if ($stmt->execute()) {
            // Redirect to login page with success message
            header("Location: login.php?registration=success");
            exit();
        } else {
            $errors[] = "Błąd rejestracji: " . $stmt->error;
        }

        $stmt->close();
    }

    // If there are errors, redirect back to registration with error messages
    if (!empty($errors)) {
        $error_string = urlencode(implode("|", $errors));
        header("Location: register.php?errors=" . $error_string);
        exit();
    }
}

$conn->close();
?>
