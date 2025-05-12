<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "szkola_jazdy";

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(['status' => 'error', 'message' => 'Nie podano ID ucznia']);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]);
    exit;
}

$schedule = [];
$response = ['status' => 'error', 'message' => 'Nie udało się pobrać harmonogramu ucznia'];

try {
    $sql = "SELECT 
                j.id AS jazda_id, 
                j.data_jazdy, 
                j.godzina_od, 
                j.godzina_do, 
                j.odbyta,
                i.imie AS instruktor_imie, 
                i.nazwisko AS instruktor_nazwisko,
                p.rejestracja AS pojazd_rejestracja
            FROM jazdy j
            JOIN instruktorzy i ON j.id_instruktora = i.id
            LEFT JOIN pojazdy p ON j.id_pojazdu = p.id
            WHERE j.id_ucznia = ?
            ORDER BY j.data_jazdy ASC, j.godzina_od ASC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Błąd przygotowania zapytania: ' . $conn->error);
    }

    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
         throw new Exception('Błąd wykonania zapytania: ' . $stmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }

    $response = ['status' => 'success', 'data' => $schedule];
    $stmt->close();

} catch (Exception $e) {
     error_log("Błąd w get_student_schedule.php: " . $e->getMessage());
     $response = ['status' => 'error', 'message' => 'Wystąpił błąd serwera przy pobieraniu harmonogramu ucznia.'];
}

$conn->close();

echo json_encode($response);

?>
