<?php
require_once 'config.php';

try {
    // Check uzytkownicy table
    echo "Struktura tabeli uzytkownicy:\n";
    $result = $conn->query("DESCRIBE uzytkownicy");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
    }
    
    echo "\nStruktura tabeli instruktorzy:\n";
    $result = $conn->query("DESCRIBE instruktorzy");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
    }

    // Check foreign key
    $result = $conn->query("
        SELECT * 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'instruktorzy' 
        AND REFERENCED_TABLE_NAME = 'uzytkownicy'
    ");
    
    if ($result->num_rows > 0) {
        echo "\nKlucz obcy jest poprawnie skonfigurowany.\n";
    } else {
        echo "\nUWAGA: Brak klucza obcego!\n";
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 