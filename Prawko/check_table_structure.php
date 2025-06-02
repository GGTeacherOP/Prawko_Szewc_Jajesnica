<?php
require_once 'config.php';

// Check if the database connection is working
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get table structure
$sql = "DESCRIBE platnosci";
$result = $conn->query($sql);

echo "<h2>Structure of platnosci table:</h2>";
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
    echo "No columns found in platnosci table";
}

echo "</pre>";

// Check foreign keys
$sql = "SELECT 
    TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'szkolajazdy'
    AND TABLE_NAME = 'platnosci'";

$result = $conn->query($sql);

echo "<h2>Foreign Keys in platnosci table:</h2>";
echo "<pre>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Table: " . $row["TABLE_NAME"] . "\n";
        echo "Column: " . $row["COLUMN_NAME"] . "\n";
        echo "Constraint: " . $row["CONSTRAINT_NAME"] . "\n";
        echo "Referenced Table: " . $row["REFERENCED_TABLE_NAME"] . "\n";
        echo "Referenced Column: " . $row["REFERENCED_COLUMN_NAME"] . "\n";
        echo "-------------------\n";
    }
} else {
    echo "No foreign keys found";
}

echo "</pre>";

$conn->close();
?> 