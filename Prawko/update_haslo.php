<?php
session_start();
require_once 'config.php';

// Validate input
$token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
$haslo = $_POST['haslo'];
$powtorz_haslo = $_POST['powtorz_haslo'];

// Check if all fields are filled
if (empty($token) || empty($haslo) || empty($powtorz_haslo)) {
    $_SESSION['error_message'] = "Wszystkie pola są wymagane.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

// Check if passwords match
if ($haslo !== $powtorz_haslo) {
    $_SESSION['error_message'] = "Hasła nie są identyczne.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

// Validate password strength
if (strlen($haslo) < 8) {
    $_SESSION['error_message'] = "Hasło musi mieć co najmniej 8 znaków.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

if (!preg_match("/[A-Z]/", $haslo)) {
    $_SESSION['error_message'] = "Hasło musi zawierać co najmniej jedną wielką literę.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

if (!preg_match("/[a-z]/", $haslo)) {
    $_SESSION['error_message'] = "Hasło musi zawierać co najmniej jedną małą literę.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

if (!preg_match("/[0-9]/", $haslo)) {
    $_SESSION['error_message'] = "Hasło musi zawierać co najmniej jedną cyfrę.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

if (!preg_match("/[^A-Za-z0-9]/", $haslo)) {
    $_SESSION['error_message'] = "Hasło musi zawierać co najmniej jeden znak specjalny.";
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get user ID from token
    $stmt = $conn->prepare("
        SELECT user_id 
        FROM reset_tokens 
        WHERE token = ? AND used = 0 AND expiry > NOW()
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Link do resetowania hasła jest nieprawidłowy lub wygasł.");
    }
    
    $user_id = $result->fetch_assoc()['user_id'];
    
    // Hash new password
    $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);
    
    // Update user's password
    $stmt = $conn->prepare("UPDATE uzytkownicy SET haslo = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas aktualizacji hasła.");
    }
    
    // Mark token as used
    $stmt = $conn->prepare("UPDATE reset_tokens SET used = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    
    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas aktualizacji tokenu.");
    }
    
    // Commit transaction
    $conn->commit();
    
    // Set success message and redirect to login
    $_SESSION['success_message'] = "Hasło zostało zmienione pomyślnie. Możesz się teraz zalogować.";
    header("Location: login.php");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_message'] = "Wystąpił błąd: " . $e->getMessage();
    header("Location: nowe_haslo.php?token=" . urlencode($token));
    exit();
}
?> 