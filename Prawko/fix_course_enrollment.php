<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $conn->begin_transaction();

    // First, let's check all courses
    $stmt = $conn->prepare("SELECT id, nazwa, kategoria, cena FROM kursy");
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo "<!DOCTYPE html>
    <html lang='pl'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Status Zapisu na Kurs - Linia Nauka Jazdy</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='container'>
            <h1>Status Zapisu na Kurs</h1>";

    // Display available courses
    echo "<h2>Dostępne kursy:</h2>";
    echo "<table class='course-table'>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Cena</th>
                <th>Akcja</th>
            </tr>";

    foreach ($courses as $course) {
        echo "<tr>";
        echo "<td>" . $course['id'] . "</td>";
        echo "<td>" . $course['nazwa'] . "</td>";
        echo "<td>" . $course['kategoria'] . "</td>";
        echo "<td>" . number_format($course['cena'], 2) . " PLN</td>";
        echo "<td>";
        
        // Check if user is already enrolled
        $stmt = $conn->prepare("SELECT status FROM zapisy WHERE uzytkownik_id = ? AND kurs_id = ?");
        $stmt->bind_param("ii", $user_id, $course['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $enrollment = $result->fetch_assoc();
            echo "Zapisany (Status: " . $enrollment['status'] . ")";
        } else {
            echo "<a href='zapisz_kurs.php?kurs_id=" . $course['id'] . "' class='btn btn-primary'>Zapisz się</a>";
        }
        
        echo "</td></tr>";
        $stmt->close();
    }
    echo "</table>";

    // Show current enrollments
    echo "<h2>Twoje zapisy:</h2>";
    $stmt = $conn->prepare("SELECT z.*, k.nazwa as kurs_nazwa, k.kategoria 
                           FROM zapisy z 
                           JOIN kursy k ON z.kurs_id = k.id 
                           WHERE z.uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table class='enrollment-table'>
            <tr>
                <th>Kurs</th>
                <th>Kategoria</th>
                <th>Status</th>
                <th>Akcja</th>
            </tr>";

    while ($enrollment = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $enrollment['kurs_nazwa'] . "</td>";
        echo "<td>" . $enrollment['kategoria'] . "</td>";
        echo "<td>" . $enrollment['status'] . "</td>";
        echo "<td>";
        
        if ($enrollment['status'] === 'Oczekujący') {
            echo "<a href='platnosci.php' class='btn btn-primary'>Przejdź do płatności</a>";
        } else {
            echo "Zatwierdzony";
        }
        
        echo "</td></tr>";
    }
    echo "</table>";

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "<div class='alert alert-error'>Wystąpił błąd: " . $e->getMessage() . "</div>";
}

echo "<div class='actions'>
        <a href='dashboard.php' class='btn'>Powrót do panelu</a>
    </div>
</div>
</body>
</html>";
?> 