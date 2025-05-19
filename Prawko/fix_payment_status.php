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

    // First, let's check the current status of enrollments and payments
    $stmt = $conn->prepare("SELECT z.*, k.nazwa as kurs_nazwa, p.status as payment_status, p.id as payment_id
                           FROM zapisy z
                           LEFT JOIN kursy k ON z.kurs_id = k.id
                           LEFT JOIN platnosci p ON z.kurs_id = p.kurs_id AND z.uzytkownik_id = p.uzytkownik_id
                           WHERE z.uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<!DOCTYPE html>
    <html lang='pl'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Status Zapisu i Płatności - Linia Nauka Jazdy</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='container'>
            <h1>Status Zapisu i Płatności</h1>";

    // Display current status
    echo "<h2>Aktualny status:</h2>";
    echo "<table class='status-table'>
            <tr>
                <th>Kurs</th>
                <th>Status Zapisu</th>
                <th>Status Płatności</th>
                <th>Akcja</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['kurs_nazwa'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . ($row['payment_status'] ?? 'Brak płatności') . "</td>";
        echo "<td>";
        
        // Fix the status if needed
        if ($row['status'] === 'Oczekujący' && $row['payment_status'] === 'Opłacony') {
            // Update enrollment status
            $update_stmt = $conn->prepare("UPDATE zapisy SET status = 'Zatwierdzony' WHERE id = ?");
            $update_stmt->bind_param("i", $row['id']);
            $update_stmt->execute();
            $update_stmt->close();
            echo "Zaktualizowano status zapisu";
        } elseif ($row['status'] === 'Zatwierdzony' && ($row['payment_status'] === 'Oczekujący' || $row['payment_status'] === NULL)) {
            // Create new payment if needed
            $course_stmt = $conn->prepare("SELECT cena FROM kursy WHERE id = ?");
            $course_stmt->bind_param("i", $row['kurs_id']);
            $course_stmt->execute();
            $course_result = $course_stmt->get_result();
            $course = $course_result->fetch_assoc();
            $course_stmt->close();

            if ($course) {
                $payment_stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) VALUES (?, ?, ?, 'Oczekujący')");
                $payment_stmt->bind_param("iid", $user_id, $row['kurs_id'], $course['cena']);
                $payment_stmt->execute();
                $payment_stmt->close();
                echo "Utworzono nową płatność";
            }
        }
        echo "</td></tr>";
    }
    echo "</table>";

    // Reset any stuck payments
    $stmt = $conn->prepare("UPDATE platnosci 
                           SET status = 'Oczekujący', 
                           data_platnosci = NULL 
                           WHERE uzytkownik_id = ? 
                           AND status = 'Oczekujący'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo "<div class='alert alert-success'>Status został zaktualizowany. Możesz teraz spróbować ponownie.</div>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<div class='alert alert-error'>Wystąpił błąd: " . $e->getMessage() . "</div>";
}

echo "<div class='actions'>
        <a href='dashboard.php' class='btn'>Powrót do panelu</a>
        <a href='platnosci.php' class='btn btn-primary'>Przejdź do płatności</a>
    </div>
</div>
</body>
</html>";
?> 