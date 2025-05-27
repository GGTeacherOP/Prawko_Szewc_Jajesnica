<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $kategoria = $_POST['kategoria_prawa_jazdy'];
    
    // Validate category
    $allowed_categories = ['A', 'B', 'C', 'D'];
    if (!in_array($kategoria, $allowed_categories)) {
        $_SESSION['error_message'] = "Nieprawidłowa kategoria prawa jazdy.";
        header("Location: dashboard.php");
        exit();
    }
    
    try {
        // Check if column exists
        $check_column = $conn->query("SHOW COLUMNS FROM uzytkownicy LIKE 'kategoria_prawa_jazdy'");
        
        if ($check_column->num_rows === 0) {
            // Column doesn't exist, create it
            $create_column = "ALTER TABLE uzytkownicy ADD COLUMN kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') DEFAULT NULL";
            $conn->query($create_column);
        }
        
        // Update user's category
        $stmt = $conn->prepare("UPDATE uzytkownicy SET kategoria_prawa_jazdy = ? WHERE id = ?");
        $stmt->bind_param("si", $kategoria, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Kategoria prawa jazdy została zaktualizowana.";
        } else {
            $_SESSION['error_message'] = "Wystąpił błąd podczas aktualizacji kategorii.";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Wystąpił błąd podczas aktualizacji: " . $e->getMessage();
    }
    
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed directly without POST data
    header("Location: dashboard.php");
    exit();
}
?> 