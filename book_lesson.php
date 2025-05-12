<?php
header('Content-Type: application/json');
include 'db_connect.php'; // Dołącz plik połączenia z bazą

// Odczytaj dane wejściowe JSON
$input = json_decode(file_get_contents('php://input'), true);

// Logowanie otrzymanych danych
error_log("[book_lesson.php] Otrzymano dane wejściowe: " . print_r($input, true));

// Walidacja danych wejściowych (podstawowa)
if (!isset($input['userId'], $input['instructorId'], $input['date'], $input['startTime'], $input['endTime'])) {
    error_log("[book_lesson.php] Błąd: Brakujące dane wejściowe."); // Dodano logowanie błędu walidacji
    echo json_encode(['status' => 'error', 'message' => 'Brakujące dane do rezerwacji.']);
    exit();
}

$userId = $input['userId']; // ID zalogowanego ucznia
$instructorId = $input['instructorId']; // ID instruktora (bezpośrednio)
$date = $input['date'];
$startTime = $input['startTime']; // Godzina od
$endTime = $input['endTime']; // Godzina do

// --- Sprawdzenie kolizji terminów --- 
// Sprawdź, czy instruktor LUB uczeń mają już jazdę w tym czasie
$stmtCheck = $conn->prepare("
    SELECT id FROM jazdy 
    WHERE data_jazdy = ? 
    AND (
        (id_instruktora = ? AND NOT (godzina_do <= ? OR godzina_od >= ?)) OR
        (id_ucznia = ? AND NOT (godzina_do <= ? OR godzina_od >= ?))
    )
");
$stmtCheck->bind_param("sisssis", $date, $instructorId, $startTime, $endTime, $userId, $startTime, $endTime);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Wybrany termin jest już zajęty (kolizja z inną jazdą instruktora lub Twoją).']);
    $stmtCheck->close();
    $conn->close();
    exit();
}
$stmtCheck->close();

// --- Dodanie rezerwacji do bazy --- 
$stmtInsert = $conn->prepare("INSERT INTO jazdy (id_ucznia, id_instruktora, data_jazdy, godzina_od, godzina_do, status) VALUES (?, ?, ?, ?, ?, 'Zaplanowana')");
$stmtInsert->bind_param("iisss", $userId, $instructorId, $date, $startTime, $endTime);

if ($stmtInsert->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Jazda została pomyślnie zarezerwowana!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Błąd podczas rezerwacji jazdy: ' . $stmtInsert->error]);
}

$stmtInsert->close();
$conn->close();
?>
