<?php
header('Content-Type: application/json');

// Pobierz dane z żądania
$data = json_decode(file_get_contents('php://input'), true);

// Sprawdź czy wszystkie wymagane pola są obecne
if (!isset($data['login']) || !isset($data['password']) || !isset($data['name']) || 
    !isset($data['surname']) || !isset($data['email']) || !isset($data['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Brak wymaganych danych']);
    exit;
}

// Połączenie z bazą danych
try {
    $db = new PDO('mysql:host=localhost;dbname=szkola_jazdy;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych']);
    exit;
}

// Sprawdź czy login jest już zajęty
$stmt = $db->prepare('SELECT id FROM uzytkownicy WHERE login = ?');
$stmt->execute([$data['login']]);
if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ten login jest już zajęty']);
    exit;
}

// Sprawdź czy email jest już zajęty
$stmt = $db->prepare('SELECT id FROM uzytkownicy WHERE email = ?');
$stmt->execute([$data['email']]);
if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ten email jest już używany']);
    exit;
}

// Zahaszuj hasło
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

try {
    // Rozpocznij transakcję
    $db->beginTransaction();

    // Dodaj użytkownika do tabeli uzytkownicy
    $stmt = $db->prepare('INSERT INTO uzytkownicy (login, haslo, email, rola) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['login'],
        $hashedPassword,
        $data['email'],
        $data['role']
    ]);
    
    $userId = $db->lastInsertId();

    // Dodaj dane do odpowiedniej tabeli w zależności od roli
    if ($data['role'] === 'Uczniem') {
        $stmt = $db->prepare('INSERT INTO uczniowie (id_uzytkownika, imie, nazwisko) VALUES (?, ?, ?)');
    } else {
        $stmt = $db->prepare('INSERT INTO instruktorzy (id_uzytkownika, imie, nazwisko) VALUES (?, ?, ?)');
    }
    
    $stmt->execute([
        $userId,
        $data['name'],
        $data['surname']
    ]);

    // Zatwierdź transakcję
    $db->commit();

    echo json_encode(['status' => 'success', 'message' => 'Rejestracja zakończona pomyślnie']);

} catch(PDOException $e) {
    // W przypadku błędu, wycofaj transakcję
    $db->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Błąd podczas rejestracji użytkownika']);
    exit;
}
?>
