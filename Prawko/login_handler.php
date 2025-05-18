<?php
session_start();
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
    // Sanitize inputs
    $email = sanitize_input($_POST['email']);
    $haslo = $_POST['haslo'];

    // Validate inputs
    $errors = [];

    if (empty($email)) {
        $errors[] = "Email jest wymagany.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Nieprawidłowy format emaila.";
    }

    if (empty($haslo)) {
        $errors[] = "Hasło jest wymagane.";
    }

    // If no validation errors, proceed with login
    if (empty($errors)) {
        try {
            // Pobierz użytkownika z bazy
            $stmt = $conn->prepare("SELECT id, haslo, rola FROM uzytkownicy WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($haslo, $user['haslo'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['rola'] = $user['rola'];
                    
                    // Przekieruj w zależności od roli
                    if ($user['rola'] === 'instruktor') {
                        header("Location: panel_instruktora.php");
                    } else {
                        header("Location: dashboard.php");
                    }
                    exit();
                } else {
                    throw new Exception("Nieprawidłowe hasło.");
                }
            } else {
                throw new Exception("Nie znaleziono użytkownika o podanym emailu.");
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: login.php");
            exit();
        }
    }

    // If there are errors, redirect back to login with error messages
    if (!empty($errors)) {
        $error_string = urlencode(implode("|", $errors));
        header("Location: login.php?errors=" . $error_string);
        exit();
    }
}

$conn->close();
?>
