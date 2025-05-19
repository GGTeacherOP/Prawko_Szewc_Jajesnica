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

    // First, let's check the current state of everything
    echo "<!DOCTYPE html>
    <html lang='pl'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Naprawa Płatności - Linia Nauka Jazdy</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='container'>
            <h1>Naprawa Płatności i Zapisów</h1>";

    // Check current payments
    $stmt = $conn->prepare("SELECT p.*, k.nazwa as kurs_nazwa 
                           FROM platnosci p 
                           LEFT JOIN kursy k ON p.kurs_id = k.id 
                           WHERE p.uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo "<h2>Aktualne płatności:</h2>";
    echo "<table class='payment-table'>
            <tr>
                <th>ID</th>
                <th>Kurs</th>
                <th>Kwota</th>
                <th>Status</th>
                <th>Data</th>
            </tr>";

    foreach ($payments as $payment) {
        echo "<tr>";
        echo "<td>" . $payment['id'] . "</td>";
        echo "<td>" . ($payment['kurs_nazwa'] ?? 'Brak kursu') . "</td>";
        echo "<td>" . number_format($payment['kwota'], 2) . " PLN</td>";
        echo "<td>" . $payment['status'] . "</td>";
        echo "<td>" . ($payment['data_platnosci'] ?? 'Brak') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check current enrollments
    $stmt = $conn->prepare("SELECT z.*, k.nazwa as kurs_nazwa 
                           FROM zapisy z 
                           JOIN kursy k ON z.kurs_id = k.id 
                           WHERE z.uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $enrollments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo "<h2>Aktualne zapisy:</h2>";
    echo "<table class='enrollment-table'>
            <tr>
                <th>ID</th>
                <th>Kurs</th>
                <th>Status</th>
            </tr>";

    foreach ($enrollments as $enrollment) {
        echo "<tr>";
        echo "<td>" . $enrollment['id'] . "</td>";
        echo "<td>" . $enrollment['kurs_nazwa'] . "</td>";
        echo "<td>" . $enrollment['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Now fix the issues
    echo "<h2>Naprawianie problemów...</h2>";

    // 1. Delete all pending payments
    $stmt = $conn->prepare("DELETE FROM platnosci 
                           WHERE uzytkownik_id = ? 
                           AND status = 'Oczekujący'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // 2. Reset all enrollments to 'Oczekujący'
    $stmt = $conn->prepare("UPDATE zapisy 
                           SET status = 'Oczekujący' 
                           WHERE uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // 3. Create new payments for each enrollment
    foreach ($enrollments as $enrollment) {
        // Get course price
        $stmt = $conn->prepare("SELECT cena FROM kursy WHERE id = ?");
        $stmt->bind_param("i", $enrollment['kurs_id']);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($course) {
            // Create new payment
            $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) 
                                  VALUES (?, ?, ?, 'Oczekujący')");
            $stmt->bind_param("iid", $user_id, $enrollment['kurs_id'], $course['cena']);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    echo "<div class='alert alert-success'>Wszystkie problemy zostały naprawione. Możesz teraz spróbować ponownie.</div>";

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