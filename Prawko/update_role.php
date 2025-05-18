<?php
require_once 'config.php';

try {
    $sql = "ALTER TABLE uzytkownicy ADD COLUMN IF NOT EXISTS rola ENUM('kursant', 'instruktor') DEFAULT 'kursant' NOT NULL;";
    if ($conn->query($sql)) {
        echo "Kolumna 'rola' została dodana pomyślnie.";
    }
} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}
?> 