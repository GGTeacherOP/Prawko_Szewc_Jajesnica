<?php
$servername = "localhost";
$username = "root"; // Domyślny użytkownik XAMPP
$password = ""; // Domyślne puste hasło XAMPP
$dbname = "szkola_jazdy";

// Utwórz połączenie
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdź połączenie
if ($conn->connect_error) {
  // Zamiast die(), lepiej zwrócić błąd JSON w rzeczywistych skryptach API
  // die("Connection failed: " . $conn->connect_error);
  header('Content-Type: application/json');
  echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]);
  exit(); // Zakończ skrypt, jeśli nie można się połączyć
}

// Ustawienie kodowania na UTF-8
$conn->set_charset("utf8");

?>
