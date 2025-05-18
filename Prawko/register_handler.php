<?php
session_start();
require_once 'config.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_data = $_POST;
    
    try {
        // Sprawdź czy hasła się zgadzają
        if ($_POST['haslo'] !== $_POST['powtorz_haslo']) {
            throw new Exception("Hasła nie są takie same");
        }

        // Sprawdź czy email już istnieje
        $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Ten email jest już zajęty");
        }

        // Sprawdź czy login już istnieje
        $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE login = ?");
        $stmt->bind_param("s", $_POST['login']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Ten login jest już zajęty");
        }

        // Validate required fields
        $required_fields = ['imie', 'nazwisko', 'email', 'telefon', 'login', 'haslo', 'data_urodzenia', 'kategoria_prawa_jazdy'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Pole " . $field . " jest wymagane");
            }
        }

        // Check if instructor registration
        $is_instructor = isset($_POST['is_instructor']) && $_POST['is_instructor'] == '1';
        if ($is_instructor) {
            if (!isset($_POST['instructor_password']) || $_POST['instructor_password'] !== '123') {
                throw new Exception("Nieprawidłowe hasło weryfikacyjne instruktora");
            }
            if (!isset($_POST['instructor_categories']) || empty($_POST['instructor_categories'])) {
                throw new Exception("Wybierz co najmniej jedną kategorię uprawnień instruktora");
            }
        }

        // Start transaction
        $conn->begin_transaction();

        // Insert user
        $stmt = $conn->prepare("
            INSERT INTO uzytkownicy (imie, nazwisko, email, telefon, login, haslo, data_urodzenia, kategoria_prawa_jazdy, rola)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $hashed_password = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
        $rola = $is_instructor ? 'instruktor' : 'kursant';
        
        $stmt->bind_param("sssssssss", 
            $_POST['imie'],
            $_POST['nazwisko'],
            $_POST['email'],
            $_POST['telefon'],
            $_POST['login'],
            $hashed_password,
            $_POST['data_urodzenia'],
            $_POST['kategoria_prawa_jazdy'],
            $rola
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas dodawania użytkownika: " . $stmt->error);
        }

        $user_id = $conn->insert_id;

        // If instructor, add to instructors table
        if ($is_instructor) {
            // For SET type, we need to join with commas
            $kategorie = implode(',', array_unique($_POST['instructor_categories']));
            $stmt = $conn->prepare("
                INSERT INTO instruktorzy (imie, nazwisko, email, telefon, kategorie_uprawnien)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss",
                $_POST['imie'],
                $_POST['nazwisko'],
                $_POST['email'],
                $_POST['telefon'],
                $kategorie
            );
            if (!$stmt->execute()) {
                throw new Exception("Błąd podczas dodawania instruktora: " . $stmt->error . " SQL: " . $stmt->sqlstate);
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Konto zostało utworzone pomyślnie. Możesz się teraz zalogować.";
        header("Location: login.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_messages'] = array($e->getMessage());
        $_SESSION['form_data'] = $form_data;
        header("Location: register.php");
        exit();
    }
}

$conn->close();
?>
