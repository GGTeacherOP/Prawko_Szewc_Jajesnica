<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all payments for the user
$stmt = $conn->prepare("SELECT p.*, k.nazwa as kurs_nazwa 
                       FROM platnosci p 
                       LEFT JOIN kursy k ON p.kurs_id = k.id 
                       WHERE p.uzytkownik_id = ? 
                       ORDER BY p.data_platnosci DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Status płatności:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Kurs</th><th>Kwota</th><th>Status</th><th>Data płatności</th></tr>";

while ($payment = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $payment['id'] . "</td>";
    echo "<td>" . ($payment['kurs_nazwa'] ?? 'Brak kursu') . "</td>";
    echo "<td>" . $payment['kwota'] . " PLN</td>";
    echo "<td>" . $payment['status'] . "</td>";
    echo "<td>" . $payment['data_platnosci'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$stmt->close();
?> 