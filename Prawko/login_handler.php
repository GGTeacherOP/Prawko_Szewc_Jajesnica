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
    $login = sanitize_input($_POST['login']);
    $haslo = $_POST['haslo'];

    // Validate inputs
    $errors = [];

    if (empty($login)) {
        $errors[] = "Login jest wymagany.";
    }

    if (empty($haslo)) {
        $errors[] = "Hasło jest wymagane.";
    }

    // If no validation errors, proceed with login
    if (empty($errors)) {
        // Prepare SQL statement to check user credentials
        $login_query = "SELECT id, imie, nazwisko, haslo FROM uzytkownicy WHERE login = ?";
        
        $stmt = $conn->prepare($login_query);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($haslo, $user['haslo'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['imie'] . ' ' . $user['nazwisko'];
                
                // Redirect to dashboard or home page
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Nieprawidłowe hasło.";
            }
        } else {
            $errors[] = "Użytkownik o podanym loginie nie istnieje.";
        }

        $stmt->close();
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
