<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako instruktor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $availability_id = $_GET['id'];
    
    // Sprawdź, czy dostępność należy do zalogowanego instruktora
    $check_query = "SELECT 1 FROM dostepnosc_instruktorow 
                   WHERE id = ? AND instruktor_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $availability_id, $instruktor_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Sprawdź, czy nie ma zaplanowanych jazd w tym terminie
        $check_jazdy_query = "SELECT j.* FROM jazdy j
                            JOIN dostepnosc_instruktorow d ON 
                            j.instruktor_id = d.instruktor_id AND
                            DATE(j.data_jazdy) = d.data AND
                            TIME(j.data_jazdy) BETWEEN d.godzina_od AND d.godzina_do
                            WHERE d.id = ? AND j.status = 'Zaplanowana'";
        $check_jazdy_stmt = $conn->prepare($check_jazdy_query);
        $check_jazdy_stmt->bind_param("i", $availability_id);
        $check_jazdy_stmt->execute();
        $jazdy_result = $check_jazdy_stmt->get_result();
        
        if ($jazdy_result->num_rows > 0) {
            $_SESSION['message'] = "Nie można usunąć dostępności - masz zaplanowane jazdy w tym terminie.";
            $_SESSION['message_type'] = "error";
        } else {
            // Usuń dostępność
            $delete_query = "DELETE FROM dostepnosc_instruktorow WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $availability_id);
            
            if ($delete_stmt->execute()) {
                $_SESSION['message'] = "Dostępność została usunięta pomyślnie.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Wystąpił błąd podczas usuwania dostępności.";
                $_SESSION['message_type'] = "error";
            }
        }
    } else {
        $_SESSION['message'] = "Nie znaleziono dostępności lub nie masz uprawnień do jej usunięcia.";
        $_SESSION['message_type'] = "error";
    }
}

header("Location: ustaw_dostepnosc.php");
exit(); 