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
    // Check if login is email
    $is_email = filter_var($login, FILTER_VALIDATE_EMAIL);
    error_log("Is email format: " . ($is_email ? "yes" : "no"));
    
    // Prepare query based on login type
    if ($is_email) {
        $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE email = ?");
        error_log("Searching by email");
    } else {
        $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE login = ?");
        error_log("Searching by login");
    }
    
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Query results found: " . $result->num_rows);
    
    if ($result->num_rows === 0) {
        error_log("Error: No user found");
        header("Location: login.php?errors=" . urlencode("Nieprawidłowy login lub hasło."));
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Log user data (except password)
    error_log("User found:");
    error_log("ID: " . $user['id']);
    error_log("Login: " . $user['login']);
    error_log("Email: " . $user['email']);
    error_log("Role: " . ($user['rola'] ?? 'not set'));
    error_log("Stored password hash length: " . strlen($user['haslo']));
    
    // Verify password
    $password_verify_result = password_verify($haslo, $user['haslo']);
    error_log("Password verification result: " . ($password_verify_result ? "success" : "failed"));
    
    if (!$password_verify_result) {
        error_log("Error: Password verification failed");
        header("Location: login.php?errors=" . urlencode("Nieprawidłowy login lub hasło."));
        exit();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['imie'] . ' ' . $user['nazwisko'];
    $_SESSION['rola'] = $user['rola'];
    $_SESSION['user_email'] = $user['email'];
    
    error_log("Session variables set:");
    error_log("user_id: " . $_SESSION['user_id']);
    error_log("user_name: " . $_SESSION['user_name']);
    error_log("rola: " . $_SESSION['rola']);
    error_log("user_email: " . $_SESSION['user_email']);
    
    // Redirect based on role
    switch ($user['rola']) {
        case 'admin':
            error_log("Redirecting to admin dashboard");
            header("Location: admin/dashboard.php");
            break;
        case 'instruktor':
            error_log("Redirecting to instructor panel");
            header("Location: panel_instruktora.php");
            break;
        case 'kursant':
        default:
            error_log("Redirecting to student dashboard");
            header("Location: dashboard.php");
            break;
    }
    exit();
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header("Location: login.php?errors=" . urlencode("Wystąpił błąd podczas logowania: " . $e->getMessage()));
    exit();
}
?>
