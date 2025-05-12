<?php
header('Content-Type: application/json');

$servername = "localhost"; 
$username = "root"; 
$db_password = ""; // Używamy poprawnej zmiennej dla hasła DB
$dbname = "szkola_jazdy";

// Połączenie z bazą danych
$conn = new mysqli($servername, $username, $db_password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]);
    exit;
}

$instructors = [];
$sql = "SELECT id, imie, nazwisko FROM instruktorzy ORDER BY nazwisko, imie";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $instructors[] = $row; // Dodajemy cały wiersz (id, imie, nazwisko)
    }
    echo json_encode(['status' => 'success', 'instructors' => $instructors]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Nie znaleziono instruktorów.']);
}

$conn->close();
?>
