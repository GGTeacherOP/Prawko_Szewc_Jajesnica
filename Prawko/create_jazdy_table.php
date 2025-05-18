<?php
require_once 'config.php';

// Wczytaj i wykonaj zapytanie SQL
$sql = file_get_contents('create_jazdy_table.sql');
if ($conn->multi_query($sql)) {
    echo "Tabela jazdy została utworzona pomyślnie.\n";
} else {
    echo "Błąd podczas tworzenia tabeli: " . $conn->error . "\n";
}

$conn->close();
?> 