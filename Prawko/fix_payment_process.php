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

    // First, let's check the current state
    echo "<!DOCTYPE html>
    <html lang='pl'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Naprawa Procesu Płatności - Linia Nauka Jazdy</title>
        <link rel='stylesheet' href='styles.css'>
    </head>
    <body>
        <div class='container'>
            <h1>Naprawa Procesu Płatności</h1>";

    // 1. Delete ALL payments for this user
    $stmt = $conn->prepare("DELETE FROM platnosci WHERE uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // 2. Get all enrollments
    $stmt = $conn->prepare("SELECT z.*, k.nazwa as kurs_nazwa, k.cena 
                           FROM zapisy z 
                           JOIN kursy k ON z.kurs_id = k.id 
                           WHERE z.uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $enrollments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo "<h2>Twoje zapisy:</h2>";
    echo "<table class='enrollment-table'>
            <tr>
                <th>Kurs</th>
                <th>Status</th>
                <th>Akcja</th>
            </tr>";

    foreach ($enrollments as $enrollment) {
        echo "<tr>";
        echo "<td>" . $enrollment['kurs_nazwa'] . "</td>";
        echo "<td>" . $enrollment['status'] . "</td>";
        echo "<td>";
        
        // Create new payment for this enrollment
        $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) 
                              VALUES (?, ?, ?, 'Oczekujący')");
        $stmt->bind_param("iid", $user_id, $enrollment['kurs_id'], $enrollment['cena']);
        $stmt->execute();
        $payment_id = $stmt->insert_id;
        $stmt->close();

        echo "<a href='platnosci.php?kurs_id=" . $enrollment['kurs_id'] . "' class='btn btn-primary'>Zapłać teraz</a>";
        echo "</td></tr>";
    }
    echo "</table>";

    // 3. Update all enrollments to 'Oczekujący'
    $stmt = $conn->prepare("UPDATE zapisy SET status = 'Oczekujący' WHERE uzytkownik_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo "<div class='alert alert-success'>Proces płatności został zresetowany. Możesz teraz spróbować ponownie.</div>";

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