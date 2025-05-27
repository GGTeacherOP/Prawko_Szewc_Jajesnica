<?php
require_once 'config.php';

try {
    // Step 1: Drop the existing table
    $sql1 = "DROP TABLE IF EXISTS instruktorzy";
    if (!$conn->query($sql1)) {
        throw new Exception("Błąd podczas usuwania tabeli: " . $conn->error);
    }
    echo "Stara tabela została usunięta.\n";

    // Step 2: Create new table
    $sql2 = "CREATE TABLE instruktorzy (
        uzytkownik_id INT(11),
        imie VARCHAR(50) NOT NULL,
        nazwisko VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        kategoria VARCHAR(100) NOT NULL,
        PRIMARY KEY (uzytkownik_id),
        CONSTRAINT fk_uzytkownik FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql2)) {
        echo "Nowa tabela została utworzona pomyślnie.";
    } else {
        throw new Exception("Błąd podczas tworzenia tabeli: " . $conn->error);
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

$conn->close();
?> 