<?php
header('Content-Type: application/json');

try {
    // Połączenie z bazą danych używając PDO
    $db = new PDO('mysql:host=localhost;dbname=szkola_jazdy;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pobierz dane z żądania
    $data = json_decode(file_get_contents('php://input'), true);
    $login = $data['login'] ?? null;
    $password = $data['password'] ?? null;

    if (!$login || !$password) {
        throw new Exception('Nie podano loginu lub hasła');
    }

    // Pobierz dane użytkownika
    $stmt = $db->prepare('SELECT u.id, u.haslo, u.rola, 
        CASE 
            WHEN u.rola = "Uczniem" THEN uc.imie
            WHEN u.rola = "Instruktorem" THEN i.imie
        END as imie,
        CASE 
            WHEN u.rola = "Uczniem" THEN uc.nazwisko
            WHEN u.rola = "Instruktorem" THEN i.nazwisko
        END as nazwisko
        FROM uzytkownicy u
        LEFT JOIN uczniowie uc ON u.id = uc.id_uzytkownika
        LEFT JOIN instruktorzy i ON u.id = i.id_uzytkownika
        WHERE u.login = ?');
    
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Nieprawidłowy login lub hasło');
    }

    // Weryfikuj hasło
    if (!password_verify($password, $user['haslo'])) {
        throw new Exception('Nieprawidłowy login lub hasło');
    }

    // Przygotuj odpowiedź
    echo json_encode([
        'status' => 'success',
        'userId' => $user['id'],
        'userName' => $user['imie'] . ' ' . $user['nazwisko'],
        'userRole' => $user['rola']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
