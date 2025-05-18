<?php
require_once 'config.php';

try {
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'uzytkownicy'");
    if ($result->num_rows == 0) {
        echo "Tabela uzytkownicy nie istnieje!\n";
        
        // Create users table if it doesn't exist
        $sql = "CREATE TABLE uzytkownicy (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(50) UNIQUE NOT NULL,
            haslo VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            rola VARCHAR(20) NOT NULL DEFAULT 'user'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->query($sql)) {
            echo "Utworzono tabelę uzytkownicy.\n";
        } else {
            throw new Exception("Błąd podczas tworzenia tabeli uzytkownicy: " . $conn->error);
        }
    } else {
        echo "Tabela uzytkownicy istnieje.\n";
        
        // Check structure
        $result = $conn->query("DESCRIBE uzytkownicy");
        echo "Struktura tabeli uzytkownicy:\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 