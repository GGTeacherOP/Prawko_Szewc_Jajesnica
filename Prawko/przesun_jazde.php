<?php
session_start();
require_once 'config.php';

// Sprawdź czy użytkownik jest zalogowany i czy jest instruktorem
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jazda_id = $_POST['jazda_id'];
    $nowa_data = $_POST['nowa_data'];
    $nowa_godzina = $_POST['nowa_godzina'];
    $instruktor_id = $_SESSION['user_id'];

    try {
        // Sprawdź czy jazda należy do tego instruktora
        $stmt = $conn->prepare("
            SELECT j.*, u.email as kursant_email 
            FROM jazdy j 
            JOIN uzytkownicy u ON j.uzytkownik_id = u.id 
            WHERE j.id = ? AND j.instruktor_id = ? AND j.status = 'Zaplanowana'
        ");
        $stmt->bind_param("ii", $jazda_id, $instruktor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Nie znaleziono takiej jazdy lub nie masz uprawnień do jej modyfikacji.");
        }

        $jazda = $result->fetch_assoc();

        // Sprawdź czy nowy termin nie koliduje z innymi jazdami
        $stmt = $conn->prepare("
            SELECT COUNT(*) as konflikt
            FROM jazdy 
            WHERE instruktor_id = ? AND data_jazdy = ? 
            AND ((godzina_rozpoczecia <= ? AND ADDTIME(godzina_rozpoczecia, SEC_TO_TIME(liczba_godzin * 3600)) > ?)
            OR (godzina_rozpoczecia < ADDTIME(?, SEC_TO_TIME(? * 3600)) AND godzina_rozpoczecia >= ?))
            AND status = 'Zaplanowana'
            AND id != ?
        ");
        $stmt->bind_param("issssssi", $instruktor_id, $nowa_data, $nowa_godzina, $nowa_godzina, $nowa_godzina, $jazda['liczba_godzin'], $nowa_godzina, $jazda_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $konflikt = $result->fetch_assoc()['konflikt'];

        if ($konflikt > 0) {
            throw new Exception("Wybrany termin koliduje z inną zaplanowaną jazdą.");
        }

        // Aktualizuj termin jazdy
        $stmt = $conn->prepare("
            UPDATE jazdy 
            SET data_jazdy = ?, godzina_rozpoczecia = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $nowa_data, $nowa_godzina, $jazda_id);
        $stmt->execute();

        // TODO: Można dodać wysyłanie maila do kursanta z informacją o zmianie terminu
        
        $_SESSION['success_message'] = "Termin jazdy został zmieniony.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header("Location: panel_instruktora.php");
exit(); 