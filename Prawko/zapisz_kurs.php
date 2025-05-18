<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if course type is specified
if (!isset($_GET['kategoria']) && !isset($_GET['typ'])) {
    header("Location: index.php");
    exit();
}

// Get user ID and course type
$user_id = $_SESSION['user_id'];
$kategoria = isset($_GET['kategoria']) ? $_GET['kategoria'] : null;
$typ = isset($_GET['typ']) ? $_GET['typ'] : null;

// Get course ID based on category or type
$kurs_id = null;
$errors = [];

try {
    if ($kategoria) {
        // Map kategoria to proper course name
        $course_name = '';
        switch ($kategoria) {
            case 'prawo_jazdy':
                $course_name = 'Kurs Prawa Jazdy';
                break;
            case 'instruktor':
                $course_name = 'Kurs Instruktorski';
                break;
            case 'kierowca_zawodowy':
                $course_name = 'Kurs Kierowcy Zawodowego';
                break;
            case 'operator_maszyn':
                $course_name = 'Kurs Operatora Maszyn';
                break;
            default:
                throw new Exception("Nieprawidłowa kategoria kursu.");
        }

        // Get course ID
        $stmt = $conn->prepare("SELECT id FROM kursy WHERE nazwa = ?");
        $stmt->bind_param("s", $course_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Nie znaleziono kursu o podanej kategorii.");
        }
        
        $kurs = $result->fetch_assoc();
        $kurs_id = $kurs['id'];
        $stmt->close();
    } else if ($typ) {
        // Map typ to proper course name
        $course_name = '';
        switch ($typ) {
            case 'podstawowy':
                $course_name = 'Kurs Podstawowy';
                break;
            case 'rozszerzony':
                $course_name = 'Kurs Rozszerzony';
                break;
            case 'kwalifikacja':
                $course_name = 'Kwalifikacja Zawodowa';
                break;
            default:
                throw new Exception("Nieprawidłowy typ kursu.");
        }

        // Get course ID
        $stmt = $conn->prepare("SELECT id FROM kursy WHERE nazwa = ?");
        $stmt->bind_param("s", $course_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Nie znaleziono kursu o podanym typie.");
        }
        
        $kurs = $result->fetch_assoc();
        $kurs_id = $kurs['id'];
        $stmt->close();
    }

    // Check if user is already enrolled in this course
    $stmt = $conn->prepare("SELECT id FROM zapisy WHERE uzytkownik_id = ? AND kurs_id = ?");
    $stmt->bind_param("ii", $user_id, $kurs_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("Jesteś już zapisany na ten kurs.");
    }
    $stmt->close();

    // Create enrollment
    $stmt = $conn->prepare("INSERT INTO zapisy (uzytkownik_id, kurs_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $kurs_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas zapisywania na kurs: " . $stmt->error);
    }
    
    $stmt->close();

    // Create payment record
    $stmt = $conn->prepare("SELECT cena FROM kursy WHERE id = ?");
    $stmt->bind_param("i", $kurs_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kurs = $result->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $user_id, $kurs_id, $kurs['cena']);
    
    if (!$stmt->execute()) {
        throw new Exception("Błąd podczas tworzenia płatności: " . $stmt->error);
    }
    
    $stmt->close();

    // Redirect to dashboard with success message
    header("Location: dashboard.php?enrollment=success");
    exit();

} catch (Exception $e) {
    $error_string = urlencode($e->getMessage());
    header("Location: dashboard.php?error=" . $error_string);
    exit();
}

$conn->close();
?> 