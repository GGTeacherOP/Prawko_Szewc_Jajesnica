<?php
require_once 'config.php';

// Get all courses
$query = "SELECT * FROM kursy";
$result = $conn->query($query);

echo "<h2>Dostępne kursy:</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nazwa</th><th>Kategoria</th><th>Cena</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nazwa'] . "</td>";
        echo "<td>" . $row['kategoria'] . "</td>";
        echo "<td>" . $row['cena'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Brak kursów w bazie danych.";
}

$conn->close();
?> 