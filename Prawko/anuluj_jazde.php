<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['jazda_id'])) {
    header("Location: planowanie_jazd.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$jazda_id = $_POST['jazda_id'];

try {
    // Check if the lesson belongs to the user and is not already cancelled
    $stmt = $conn->prepare("
        SELECT * FROM jazdy 
        WHERE id = ? AND uzytkownik_id = ? AND status = 'Zaplanowana'
    ");
    $stmt->bind_param("ii", $jazda_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Nie znaleziono jazdy lub nie masz uprawnień do jej anulowania.");
    }

    // Update lesson status to cancelled
    $stmt = $conn->prepare("UPDATE jazdy SET status = 'Anulowana' WHERE id = ?");
    $stmt->bind_param("i", $jazda_id);
    $stmt->execute();

    $_SESSION['success_message'] = "Jazda została anulowana pomyślnie.";
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

header("Location: planowanie_jazd.php");
exit(); 