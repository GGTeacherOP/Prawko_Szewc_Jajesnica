<?php
require_once 'config.php';

try {
    // Step 1: Add 'rola' column to uzytkownicy if it doesn't exist
    $result = $conn->query("SHOW COLUMNS FROM uzytkownicy LIKE 'rola'");
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE uzytkownicy ADD COLUMN rola VARCHAR(20) NOT NULL DEFAULT 'user'";
        if (!$conn->query($sql)) {
            throw new Exception("Błąd podczas dodawania kolumny rola: " . $conn->error);
        }
        echo "Dodano kolumnę 'rola' do tabeli uzytkownicy.\n";
    }

    // Step 2: Drop existing instruktorzy table
    $conn->query("DROP TABLE IF EXISTS instruktorzy");
    echo "Usunięto starą tabelę instruktorzy.\n";

    // Step 3: Create new instruktorzy table
    $sql = "CREATE TABLE instruktorzy (
        uzytkownik_id INT(11),
        imie VARCHAR(50) NOT NULL,
        nazwisko VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        kategoria VARCHAR(100) NOT NULL,
        PRIMARY KEY (uzytkownik_id),
        CONSTRAINT fk_uzytkownik FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql)) {
        echo "Utworzono nową tabelę instruktorzy.\n";
    } else {
        throw new Exception("Błąd podczas tworzenia tabeli instruktorzy: " . $conn->error);
    }

    echo "Wszystkie operacje zakończone pomyślnie.";

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 