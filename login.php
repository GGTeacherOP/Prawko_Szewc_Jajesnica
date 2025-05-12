<?php
header('Content-Type: application/json');

$servername = "localhost"; 
$username = "root"; 
$db_password = ""; 
$dbname = "szkola_jazdy";

$data = json_decode(file_get_contents('php://input'), true);
$login = $data['login'] ?? null;
$password = $data['password'] ?? null;

if (!$login || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Nie podano loginu lub hasła']);
    exit;
}

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych: ' . $conn->connect_error]);
    exit;
}

$login = $conn->real_escape_string($login);

$response = ['status' => 'error', 'message' => 'Nieprawidłowy login lub hasło'];

$sql_uczen = "SELECT id, imie, nazwisko, haslo FROM uczniowie WHERE login = ?";
$stmt_uczen = $conn->prepare($sql_uczen);
$stmt_uczen->bind_param("s", $login);
$stmt_uczen->execute();
$result_uczen = $stmt_uczen->get_result();

if ($result_uczen->num_rows > 0) {
    $row = $result_uczen->fetch_assoc();
    if ($password === $row['haslo']) { // Porównujemy z hasłem z bazy
        $response = [
            'status' => 'success',
            'user' => [
                'id' => $row['id'],
                'imie' => $row['imie'],
                'nazwisko' => $row['nazwisko'],
                'rola' => 'Uczniem'
            ]
        ];
    }
}
$stmt_uczen->close();

if ($response['status'] === 'error') {
    $sql_instruktor = "SELECT id, imie, nazwisko, haslo FROM instruktorzy WHERE login = ?";
    $stmt_instruktor = $conn->prepare($sql_instruktor);
    $stmt_instruktor->bind_param("s", $login);
    $stmt_instruktor->execute();
    $result_instruktor = $stmt_instruktor->get_result();

    if ($result_instruktor->num_rows > 0) {
        $row = $result_instruktor->fetch_assoc();
        if ($password === $row['haslo']) { // Porównujemy z hasłem z bazy
            $response = [
                'status' => 'success',
                'user' => [
                    'id' => $row['id'], 
                    'imie' => $row['imie'],
                    'nazwisko' => $row['nazwisko'],
                    'rola' => 'Instruktorem'
                ]
            ];
        }
    }
    $stmt_instruktor->close();
}


$conn->close();

echo json_encode($response);

?>
