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

    // Reset any stuck payments
    $stmt = $conn->prepare("UPDATE platnosci 
                           SET status = 'Oczekujący', 
                           data_platnosci = NULL 
                           WHERE uzytkownik_id = ? 
                           AND status = 'Oczekujący'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Also reset any stuck course enrollments
    $stmt = $conn->prepare("UPDATE zapisy 
                           SET status = 'Oczekujący' 
                           WHERE uzytkownik_id = ? 
                           AND status = 'Oczekujący'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['success_message'] = "Status płatności został zresetowany. Możesz teraz spróbować ponownie.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "Wystąpił błąd podczas resetowania statusu: " . $e->getMessage();
}

// Show current payment status
$stmt = $conn->prepare("SELECT p.*, k.nazwa as kurs_nazwa 
                       FROM platnosci p 
                       LEFT JOIN kursy k ON p.kurs_id = k.id 
                       WHERE p.uzytkownik_id = ? 
                       ORDER BY p.data_platnosci DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<!DOCTYPE html>
<html lang='pl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Płatności - Linia Nauka Jazdy</title>
    <link rel='stylesheet' href='styles.css'>
</head>
<body>
    <div class='container'>
        <h1>Status płatności po resecie</h1>";

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-error'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}

echo "<table class='payment-status'>
        <tr>
            <th>ID</th>
            <th>Kurs</th>
            <th>Kwota</th>
            <th>Status</th>
            <th>Data płatności</th>
        </tr>";

while ($payment = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $payment['id'] . "</td>";
    echo "<td>" . ($payment['kurs_nazwa'] ?? 'Brak kursu') . "</td>";
    echo "<td>" . $payment['kwota'] . " PLN</td>";
    echo "<td>" . $payment['status'] . "</td>";
    echo "<td>" . ($payment['data_platnosci'] ?? 'Brak') . "</td>";
    echo "</tr>";
}

echo "</table>
        <div class='actions'>
            <a href='dashboard.php' class='btn'>Powrót do panelu</a>
            <a href='platnosci.php' class='btn btn-primary'>Przejdź do płatności</a>
        </div>
    </div>
</body>
</html>";

$stmt->close();
?> 