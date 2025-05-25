<?php
session_start();
require_once 'config.php';

// Get raw input and log it
$login = trim($_POST['login'] ?? '');
$haslo = $_POST['haslo'] ?? '';

error_log("=== Login Attempt Details ===");
error_log("Login attempt for: " . $login);
error_log("Password length: " . strlen($haslo));

// Check if fields are empty
if (empty($login) || empty($haslo)) {
    error_log("Error: Empty fields detected");
    header("Location: login.php?errors=" . urlencode("Wszystkie pola są wymagane."));
    exit();
}

try {
    // Najpierw próbujemy zalogować kursanta po loginie
    $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE login = ? AND haslo = ?");
    if (!$stmt) {
        die("Błąd SQL (uzytkownicy): " . $conn->error);
    }
    $stmt->bind_param("ss", $login, $haslo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['rola'] = $user['rola'];
        $_SESSION['imie'] = $user['imie'];
        header("Location: dashboard.php");
        exit();
    }

    // Jeśli nie znaleziono kursanta, próbujemy pracownika po emailu
    $stmt = $conn->prepare("SELECT * FROM pracownicy WHERE email = ? AND haslo = ?");
    if (!$stmt) {
        die("Błąd SQL (pracownicy): " . $conn->error);
    }
    $stmt->bind_param("ss", $login, $haslo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $pracownik = $result->fetch_assoc();
        $_SESSION['user_id'] = $pracownik['id'];
        $_SESSION['rola'] = $pracownik['rola'];
        $_SESSION['imie'] = $pracownik['imie'];
        // Przekierowanie do odpowiedniego panelu
        if ($pracownik['rola'] === 'instruktor') {
            header("Location: panel_instruktora.php");
        } elseif ($pracownik['rola'] === 'ksiegowy') {
            header("Location: panel_ksiegowy.php");
        } elseif ($pracownik['rola'] === 'admin') {
            header("Location: panel_admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }

    // Jeśli nie znaleziono użytkownika ani pracownika
    header("Location: login.php?errors=" . urlencode("Nieprawidłowy login lub hasło."));
    exit();
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header("Location: login.php?errors=" . urlencode("Wystąpił błąd podczas logowania: " . $e->getMessage()));
    exit();
}
?>
