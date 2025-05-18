<?php
require_once 'config.php';

try {
    // Check if instructors table exists
    $result = $conn->query("SHOW TABLES LIKE 'instruktorzy'");
    if ($result->num_rows == 0) {
        echo "Tabela instruktorzy nie istnieje!\n";
        
        // Create instructors table
        $sql = "CREATE TABLE instruktorzy (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            imie VARCHAR(50) NOT NULL,
            nazwisko VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            telefon VARCHAR(20) NOT NULL,
            kategorie_uprawnien SET('A','B','C','D') NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->query($sql)) {
            echo "Utworzono tabelę instruktorzy.\n";
            
            // Insert sample data
            $sql = "INSERT INTO instruktorzy (imie, nazwisko, email, telefon, kategorie_uprawnien) VALUES
                ('Jan', 'Kowalski', 'jan.kowalski@example.com', '123456789', 'A,B'),
                ('Anna', 'Nowak', 'anna.nowak@example.com', '987654321', 'B,C'),
                ('Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', '555666777', 'C,D')";
            
            if ($conn->query($sql)) {
                echo "Dodano przykładowych instruktorów.\n";
            } else {
                throw new Exception("Błąd podczas dodawania instruktorów: " . $conn->error);
            }
        } else {
            throw new Exception("Błąd podczas tworzenia tabeli: " . $conn->error);
        }
    } else {
        echo "Tabela instruktorzy istnieje.\n";
        
        // Check structure
        $result = $conn->query("DESCRIBE instruktorzy");
        echo "Struktura tabeli instruktorzy:\n";
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 