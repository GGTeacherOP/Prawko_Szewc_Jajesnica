<?php
function displayEnrollmentButton($course_type, $conn, $course_name = null) {
    // Get course ID based on type and name
    if ($course_type === 'Prawo Jazdy' && isset($_GET['kategoria'])) {
        // For driving license courses, search by category
        $sql = "SELECT id, nazwa, cena FROM kursy WHERE kategoria = ? AND nazwa LIKE CONCAT('%Kat. ', ?, '%')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $course_type, $_GET['kategoria']);
    } else {
        // For other courses, use the original logic
        $sql = "SELECT id, nazwa, cena FROM kursy WHERE kategoria = ?";
        $params = [$course_type];
        
        if ($course_name) {
            $sql .= " AND nazwa = ?";
            $params[] = $course_name;
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        $course_id = $course['id'];
        $course_name = $course['nazwa'];
        $course_price = $course['cena'];
        
        echo '<div class="enrollment-section">';
        echo '<div class="course-price">Cena: ' . number_format($course_price, 2) . ' PLN</div>';
        
        if (isset($_SESSION['user_id'])) {
            if ($course_type === 'Prawo Jazdy' && isset($_GET['kategoria'])) {
                echo '<a href="zapisz_kurs.php?kategoria=' . urlencode($_GET['kategoria']) . '" class="btn primary">Zapisz się na kurs</a>';
            } else {
                echo '<a href="zapisz_kurs.php?kurs_id=' . $course_id . '" class="btn primary">Zapisz się na kurs</a>';
            }
        } else {
            echo '<a href="login.php" class="btn primary">Zaloguj się aby zapisać się na kurs</a>';
        }
        echo '</div>';
    } else {
        // NIE generuj żadnego komunikatu, jeśli kurs nie istnieje
        // (usuń info-frame z tekstem 'Prosimy o kontakt z administracją.')
    }
    $stmt->close();
}
?> 