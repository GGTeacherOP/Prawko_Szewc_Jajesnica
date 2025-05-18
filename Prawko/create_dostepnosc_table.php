<?php
require_once 'config.php';

// Wczytaj i wykonaj zapytanie SQL
$sql = file_get_contents('create_dostepnosc_table.sql');
if ($conn->multi_query($sql)) {
    echo "Tabela dostepnosc_instruktorow została utworzona pomyślnie.\n";
} else {
    echo "Błąd podczas tworzenia tabeli: " . $conn->error . "\n";
}

$conn->close();
?> 