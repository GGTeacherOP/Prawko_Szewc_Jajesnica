<?php
session_start();
require_once 'config.php';

// Validate and sanitize input
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

// Check if email is empty
if (empty($email)) {
    $_SESSION['error_message'] = "Adres email jest wymagany.";
    header("Location: reset_hasla.php");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Nieprawidłowy format adresu email.";
    header("Location: reset_hasla.php");
    exit();
}

try {
    // Check if email exists in database
    $stmt = $conn->prepare("SELECT id, imie FROM uzytkownicy WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error_message'] = "Nie znaleziono użytkownika o podanym adresie email.";
        header("Location: reset_hasla.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $stmt = $conn->prepare("INSERT INTO reset_tokens (user_id, token, expiry) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $token, $expiry);
    
    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas generowania tokenu resetowania hasła.");
    }
    
    // Send email with reset link
    $reset_link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/nowe_haslo.php?token=" . $token;
    $to = $email;
    $subject = "Reset hasła - Linia Nauka Jazdy";
    $message = "
    Witaj {$user['imie']},
    
    Otrzymaliśmy prośbę o reset hasła do Twojego konta w systemie Linia Nauka Jazdy.
    
    Aby zresetować hasło, kliknij w poniższy link:
    {$reset_link}
    
    Link jest ważny przez 1 godzinę.
    
    Jeśli nie prosiłeś o reset hasła, zignoruj tę wiadomość.
    
    Pozdrawiamy,
    Zespół Linia Nauka Jazdy
    ";
    
    $headers = "From: noreply@linianauki.pl\r\n";
    $headers .= "Reply-To: noreply@linianauki.pl\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        $_SESSION['success_message'] = "Link do resetowania hasła został wysłany na podany adres email.";
        header("Location: reset_hasla.php");
        exit();
    } else {
        throw new Exception("Błąd podczas wysyłania emaila z linkiem do resetowania hasła.");
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Wystąpił błąd: " . $e->getMessage();
    header("Location: reset_hasla.php");
    exit();
}
?> 