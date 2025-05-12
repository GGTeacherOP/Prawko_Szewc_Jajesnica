<?php
header('Content-Type: application/json');
include 'db_connect.php'; // Dołącz plik połączenia z bazą

// Odczytaj dane wejściowe
$userId = null;
$userRole = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'] ?? null;
    $userRole = $data['userRole'] ?? null;
} else { // Zachowaj kompatybilność z GET dla ewentualnych testów
    $userId = $_GET['userId'] ?? null;
    $userRole = $_GET['userRole'] ?? null;
}

if (!$userId || !$userRole) {
    echo json_encode(['status' => 'error', 'message' => 'Brak ID użytkownika lub roli.']);
    exit();
}

$sql = "";
$params = [];
$types = "";

// Przygotuj zapytanie SQL w zależności od roli
if ($userRole === 'Uczniem') {
    // Uczeń widzi swoje jazdy i dane instruktora
    $sql = "SELECT j.id, j.data_jazdy, j.godzina_od, j.godzina_do, j.status, i.imie AS imie_instruktora, i.nazwisko AS nazwisko_instruktora 
            FROM jazdy j
            JOIN instruktorzy i ON j.id_instruktora = i.id
            WHERE j.id_ucznia = ?
            ORDER BY j.data_jazdy DESC, j.godzina_od"; 
    $params = [$userId];
    $types = "i";
} elseif ($userRole === 'Instruktorem') {
    // Instruktor widzi swoje jazdy i dane ucznia
    $sql = "SELECT j.id, j.data_jazdy, j.godzina_od, j.godzina_do, j.status, u.imie AS imie_ucznia, u.nazwisko AS nazwisko_ucznia
            FROM jazdy j
            JOIN uczniowie u ON j.id_ucznia = u.id
            WHERE j.id_instruktora = ?
            ORDER BY j.data_jazdy DESC, j.godzina_od"; 
    $params = [$userId];
    $types = "i";
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nieznana rola użytkownika.']);
    exit();
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
     echo json_encode(['status' => 'error', 'message' => 'Błąd przygotowania zapytania: ' . $conn->error]);
     $conn->close();
     exit();
}

// Dynamiczne bindowanie parametrów
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$lessons = [];
if ($result->num_rows > 0) {
    $today = new DateTime(); // Dzisiejsza data do porównania
    $today->setTime(0, 0, 0); // Ustaw godzinę na początek dnia dla spójnego porównania dat

    while($row = $result->fetch_assoc()) {
        // Formatowanie godzin do HH:MM
        $row['godzina_od'] = substr($row['godzina_od'], 0, 5);
        $row['godzina_do'] = substr($row['godzina_do'], 0, 5);

        // Sprawdzenie, czy jazda jest zaplanowana i czy data jest przeszła
        $lessonDate = new DateTime($row['data_jazdy']);
        if ($row['status'] === 'Zaplanowana' && $lessonDate < $today) {
            $row['status'] = 'Zrealizowana'; 
            // Opcjonalnie: można by tu dodać UPDATE do bazy, aby stan był trwały,
            // ale na razie zmieniamy tylko w zwracanych danych.
            // $updateStmt = $conn->prepare("UPDATE jazdy SET status = 'Zrealizowana' WHERE id = ?");
            // $updateStmt->bind_param("i", $row['id']);
            // $updateStmt->execute();
            // $updateStmt->close();
        }
        
        $lessons[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'lessons' => $lessons]);
?>
