<?php
function displayEnrollmentButton($course_type, $conn, $course_name = null) {
    // Debug information
    error_log("Searching for course - Type: " . $course_type . ", Name: " . ($course_name ?? 'null'));
    
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
    
    // Debug SQL query
    error_log("SQL Query: " . $sql);
    error_log("Parameters: " . print_r($params ?? [], true));
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Debug result
    error_log("Number of results: " . $result->num_rows);
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
        $course_id = $course['id'];
        $course_name = $course['nazwa'];
        $course_price = $course['cena'];
        
        // Debug course data
        error_log("Found course - ID: " . $course_id . ", Name: " . $course_name . ", Price: " . $course_price);
        
        echo '<div class="enrollment-section">';
        echo '<div class="course-price">Cena: ' . number_format($course_price, 2) . ' PLN</div>';
        
        if (isset($_SESSION['user_id'])) {
            if ($course_type === 'Prawo Jazdy' && isset($_GET['kategoria'])) {
                echo '<a href="zapisz_kurs.php?kategoria=' . urlencode($_GET['kategoria']) . '" class="btn primary">Zapisz się na kurs</a>';
            } else {
                echo '<a href="zapisz_kurs.php?kurs_id=' . $course_id . '" class="btn primary">Zapisz się na kurs</a>';
            }
        } else {
            echo '<div class="login-required-message">';
            echo '<p><i class="fas fa-info-circle"></i> Aby zapisać się na kurs, musisz się zalogować</p>';
            echo '<a href="login.php" class="btn primary">Zaloguj się</a>';
            echo '<p class="register-link">Nie masz konta? <a href="rejestracja.php">Zarejestruj się</a></p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        // Debug when no course is found
        error_log("No course found for type: " . $course_type . " and name: " . ($course_name ?? 'null'));
    }
    $stmt->close();
}
?> 