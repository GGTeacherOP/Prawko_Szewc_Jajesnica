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
    // Try to find user by email or login
    $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE email = ? OR login = ?");
    if (!$stmt) {
        die("Błąd SQL (uzytkownicy): " . $conn->error);
    }
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $hash = $user['haslo'];
        
        // Check if password is hashed (new account)
        if (strpos($hash, '$2') === 0) {
            if (password_verify($haslo, $hash)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['rola'] = $user['rola'];
                $_SESSION['imie'] = $user['imie'];
                
                // Redirect based on role
                if ($user['rola'] === 'admin') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }
        } else {
            // Old account - direct comparison
            if ($haslo === $hash) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['rola'] = $user['rola'];
                $_SESSION['imie'] = $user['imie'];
                
                // Redirect based on role
                if ($user['rola'] === 'admin') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }
        }
    }

    error_log("No user found in uzytkownicy table, checking pracownicy table...");

    // If no user found, try employees table
    $stmt = $conn->prepare("SELECT * FROM pracownicy WHERE email = ?");
    if (!$stmt) {
        die("Błąd SQL (pracownicy): " . $conn->error);
    }
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    error_log("Found " . $result->num_rows . " employees with this email");

    if ($result->num_rows === 1) {
        $pracownik = $result->fetch_assoc();
        error_log("Employee found: " . print_r($pracownik, true));
        
        if ($pracownik['haslo'] === $haslo) {
            error_log("Password matches, setting session variables");
            $_SESSION['user_id'] = $pracownik['id'];
            $_SESSION['rola'] = $pracownik['rola'];
            $_SESSION['imie'] = $pracownik['imie'];
            
            error_log("Role: " . $pracownik['rola']);
            
            // Redirect to appropriate panel
            if ($pracownik['rola'] === 'instruktor') {
                header("Location: panel_instruktora.php");
            } elseif ($pracownik['rola'] === 'ksiegowy') {
                header("Location: panel_ksiegowy.php");
            } elseif ($pracownik['rola'] === 'admin') {
                header("Location: admin_panel.php");
            } elseif ($pracownik['rola'] === 'wlasciciel') {
                header("Location: panel_wlasciciela.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            error_log("Password does not match");
        }
    } else {
        error_log("No employee found with this email");
    }

    // If no user or employee found
    header("Location: login.php?errors=" . urlencode("Nieprawidłowy login lub hasło."));
    exit();
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header("Location: login.php?errors=" . urlencode("Wystąpił błąd podczas logowania: " . $e->getMessage()));
    exit();
}
?>
