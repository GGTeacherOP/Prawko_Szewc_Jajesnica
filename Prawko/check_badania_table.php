<?php
require_once 'config.php';

// Check if the database connection is working
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$sql = "DESCRIBE badania";
$result = $conn->query($sql);

echo "<h2>Structure of badania table:</h2>";
echo "<pre>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Field: " . $row["Field"] . "\n";
        echo "Type: " . $row["Type"] . "\n";
        echo "Null: " . $row["Null"] . "\n";
        echo "Key: " . $row["Key"] . "\n";
        echo "Default: " . $row["Default"] . "\n";
        echo "Extra: " . $row["Extra"] . "\n";
        echo "-------------------\n";
    }
} else {
    echo "No columns found in badania table";
}

echo "</pre>";

$conn->close();
?> 