<?php
require_once 'config.php';

// Check if the database connection is working
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all courses
$sql = "SELECT * FROM kursy WHERE kategoria = 'Kierowcy Zawodowi'";
$result = $conn->query($sql);

echo "<h2>Courses in database:</h2>";
echo "<pre>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "\n";
        echo "Name: " . $row["nazwa"] . "\n";
        echo "Category: " . $row["kategoria"] . "\n";
        echo "Price: " . $row["cena"] . "\n";
        echo "Description: " . $row["opis"] . "\n";
        echo "-------------------\n";
    }
} else {
    echo "No courses found";
}

echo "</pre>";

$conn->close();
?> 