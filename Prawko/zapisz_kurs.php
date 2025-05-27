<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Check if we have either course ID or category
if (!isset($_GET['kurs_id']) && !isset($_GET['kategoria'])) {
    $_SESSION['error_message'] = "Nie określono kursu.";
    header("Location: dashboard.php");
    exit();
}

try {
    // Get course details
    if (isset($_GET['kurs_id'])) {
        // If we have course ID, use it directly
        $stmt = $conn->prepare("SELECT id, nazwa, kategoria, cena FROM kursy WHERE id = ?");
        $kurs_id = $_GET['kurs_id'];
        $stmt->bind_param("i", $kurs_id);
    } else {
        // If we have category, handle both driving license and other courses
        if (isset($_GET['kategoria'])) {
            if (strpos($_GET['kategoria'], 'Kat.') === false) {
                // For driving license courses
                $stmt = $conn->prepare("SELECT id, nazwa, kategoria, cena FROM kursy WHERE kategoria = 'Prawo Jazdy' AND nazwa LIKE CONCAT('%Kat. ', ?, '%')");
                $kategoria = $_GET['kategoria'];
                $stmt->bind_param("s", $kategoria);
            } else {
                // For other courses (when full name is provided)
                $stmt = $conn->prepare("SELECT id, nazwa, kategoria, cena FROM kursy WHERE nazwa = ?");
                $stmt->bind_param("s", $_GET['kategoria']);
            }
        } else {
            throw new Exception("Nie określono kursu ani kategorii.");
        }
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Nie znaleziono kursu " . (isset($_GET['kategoria']) ? "dla kategorii " . htmlspecialchars($_GET['kategoria']) : "") . ". Proszę skontaktować się z administracją.");
    }
    
    $kurs = $result->fetch_assoc();
    $kurs_id = $kurs['id'];
    $cena = $kurs['cena'];
    $kategoria = $kurs['kategoria'];
    $stmt->close();

    // Check if user is already enrolled in this course
    $stmt = $conn->prepare("SELECT id, status FROM zapisy WHERE uzytkownik_id = ? AND kurs_id = ?");
    if ($stmt === false) {
        throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
    }
    $stmt->bind_param("ii", $user_id, $kurs_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $enrollment = $result->fetch_assoc();
        if ($enrollment['status'] === 'Oczekujący') {
            throw new Exception("Jesteś już zapisany na ten kurs. Musisz przejść badania lekarskie i opłacić kurs aby rozpocząć zajęcia.");
        } else {
            throw new Exception("Jesteś już zapisany na ten kurs.");
        }
    }
    $stmt->close();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if medical examination is required (only for driving license courses)
        $requires_medical = ($kategoria === 'Prawo Jazdy');
        $has_valid_medical = false;

        if ($requires_medical) {
            $stmt = $conn->prepare("SELECT id FROM badania 
                                  WHERE uzytkownik_id = ? 
                                  AND wynik = 'Pozytywny' 
                                  AND waznosc_do >= CURDATE()
                                  AND typ = 'Podstawowe'
                                  AND status = 'Zatwierdzony'");
            if ($stmt === false) {
                throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $has_valid_medical = $result->num_rows > 0;
            $stmt->close();
        }

        // Create enrollment with appropriate status
        $stmt = $conn->prepare("INSERT INTO zapisy (uzytkownik_id, kurs_id, status) VALUES (?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
        }
        $status = ($requires_medical && !$has_valid_medical) ? 'Oczekujący' : 'Zatwierdzony';
        $stmt->bind_param("iis", $user_id, $kurs_id, $status);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas zapisywania na kurs: " . $stmt->error);
        }
        $stmt->close();

        // Create payment record
        $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) VALUES (?, ?, ?, 'Oczekujący')");
        if ($stmt === false) {
            throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
        }
        $stmt->bind_param("iid", $user_id, $kurs_id, $cena);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas tworzenia płatności: " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Set appropriate success message
        if ($requires_medical && !$has_valid_medical) {
            $_SESSION['success_message'] = "Pomyślnie zapisano na kurs. Przed rozpoczęciem kursu musisz przejść badania lekarskie. Przejdź do sekcji 'Badania' aby umówić wizytę.";
        } else {
            $_SESSION['success_message'] = "Pomyślnie zapisano na kurs. Przejdź do sekcji 'Opłaty' aby uregulować należność.";
        }

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

    header("Location: dashboard.php");
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: dashboard.php");
    exit();
}

$conn->close();
?> 