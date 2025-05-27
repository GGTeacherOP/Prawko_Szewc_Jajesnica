<?php
session_start();
require_once 'config.php';

// Sprawdź czy użytkownik jest zalogowany i czy jest instruktorem
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jazda_id'])) {
    $jazda_id = $_POST['jazda_id'];
    $instruktor_id = $_SESSION['user_id'];

    try {
        // Sprawdź czy jazda należy do tego instruktora
        $stmt = $conn->prepare("SELECT * FROM jazdy WHERE id = ? AND instruktor_id = ? AND status = 'Zaplanowana'");
        $stmt->bind_param("ii", $jazda_id, $instruktor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Nie znaleziono takiej jazdy lub nie masz uprawnień do jej anulowania.");
        }

        // Anuluj jazdę
        $stmt = $conn->prepare("UPDATE jazdy SET status = 'Anulowana' WHERE id = ?");
        $stmt->bind_param("i", $jazda_id);
        $stmt->execute();

        $_SESSION['success_message'] = "Jazda została anulowana.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header("Location: panel_instruktora.php");
exit(); 