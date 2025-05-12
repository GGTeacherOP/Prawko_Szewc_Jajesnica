<?php
header('Content-Type: application/json');

// Konfiguracja bazy danych (taka sama jak w login.php)
$servername = "localhost";
$username = "root";
$password = ""; // Upewnij się, że hasło jest poprawne dla Twojego środowiska
$dbname = "szkola_jazdy";

// Pobranie ID instruktora z danych POST
$data = json_decode(file_get_contents('php://input'), true);
$instructor_id = $data['instructor_id'] ?? null;

if (!$instructor_id) {
    echo json_encode(['status' => 'error', 'message' => 'Nie podano ID instruktora']);
    exit;
}

// Nawiązanie połączenia z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]);
    exit;
}

$schedule = [];
$response = ['status' => 'error', 'message' => 'Nie udało się pobrać harmonogramu'];

try {
    // Zapytanie SQL pobierające jazdy dla danego instruktora
    // Łączymy z tabelą uczniowie aby pobrać imię i nazwisko ucznia
    // Łączymy z tabelą pojazdy (LEFT JOIN), aby pobrać rejestrację (jeśli jest przypisany)
    $sql = "SELECT 
                j.data_jazdy, 
                j.godzina_od, 
                j.godzina_do, 
                j.odbyta,
                u.imie AS uczen_imie, 
                u.nazwisko AS uczen_nazwisko,
                p.rejestracja AS pojazd_rejestracja
            FROM jazdy j
            JOIN uczniowie u ON j.id_ucznia = u.id
            LEFT JOIN pojazdy p ON j.id_pojazdu = p.id
            WHERE j.id_instruktora = ?
            ORDER BY j.data_jazdy ASC, j.godzina_od ASC";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception('Błąd przygotowania zapytania: ' . $conn->error);
    }

    $stmt->bind_param("i", $instructor_id);
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
    // Zapisz błąd do logów serwera w realnej aplikacji
     error_log("Błąd w get_instructor_schedule.php: " . $e->getMessage());
     $response = ['status' => 'error', 'message' => 'Wystąpił błąd serwera przy pobieraniu harmonogramu.'];
     // Możesz też zwrócić $e->getMessage() jeśli chcesz widzieć błąd w konsoli przeglądarki, ale nie rób tego w produkcji
     // $response = ['status' => 'error', 'message' => $e->getMessage()];
}

// Zamknięcie połączenia
$conn->close();

// Zwrócenie odpowiedzi JSON
echo json_encode($response);

?>
