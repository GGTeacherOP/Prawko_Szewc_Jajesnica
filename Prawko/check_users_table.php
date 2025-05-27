<?php
require_once 'config.php';

try {
    // Check users table structure
    $result = $conn->query("DESCRIBE uzytkownicy");
    echo "Struktura tabeli uzytkownicy:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
    }
} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 