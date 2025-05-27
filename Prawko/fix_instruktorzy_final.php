<?php
require_once 'config.php';

try {
    // Drop existing instruktorzy table
    $conn->query("DROP TABLE IF EXISTS instruktorzy");
    echo "Usunięto starą tabelę instruktorzy.\n";

    // Create new instruktorzy table with correct structure
    $sql = "CREATE TABLE instruktorzy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        imie VARCHAR(50) NOT NULL,
        nazwisko VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        kategorie_uprawnien SET('A','B','C','D') NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql)) {
        echo "Utworzono nową tabelę instruktorzy z poprawną strukturą.\n";
    } else {
        throw new Exception("Błąd podczas tworzenia tabeli instruktorzy: " . $conn->error);
    }

    echo "Operacja zakończona pomyślnie.";

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 