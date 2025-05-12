<?php
require_once 'db_connect.php'; // Dołączenie skryptu z połączeniem do bazy danych

// Nazwa tabeli i kolumny do sprawdzenia/dodania
$tableName = 'jazdy';
$columnName = 'status';
$columnDefinition = 'VARCHAR(20) NOT NULL DEFAULT \'Zaplanowana\' AFTER id_ucznia';

echo "<h2>Sprawdzanie i aktualizacja schematu bazy danych...</h2>";

// Zapytanie sprawdzające, czy kolumna istnieje
$checkColumnQuery = $conn->prepare(
    "SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = ? 
    AND COLUMN_NAME = ?"
);

if (!$checkColumnQuery) {
    die("Błąd przygotowania zapytania sprawdzającego kolumnę: " . $conn->error);
}

$dbName = $conn->query("SELECT DATABASE()")->fetch_row()[0]; // Pobranie nazwy aktualnej bazy danych

$checkColumnQuery->bind_param("sss", $dbName, $tableName, $columnName);
$checkColumnQuery->execute();
$checkColumnQuery->bind_result($columnExists);
$checkColumnQuery->fetch();
$checkColumnQuery->close();

if ($columnExists > 0) {
    echo "<p style='color: green;'>Kolumna '<strong>{$columnName}</strong>' już istnieje w tabeli '<strong>{$tableName}</strong>'. Nie ma potrzeby dokonywania zmian.</p>";
} else {
    echo "<p>Kolumna '<strong>{$columnName}</strong>' nie istnieje w tabeli '<strong>{$tableName}</strong>'. Próba dodania...</p>";
    
    // Zapytanie dodające kolumnę
    $alterTableQuery = "ALTER TABLE {$tableName} ADD COLUMN {$columnName} {$columnDefinition}";
    
    if ($conn->query($alterTableQuery) === TRUE) {
        echo "<p style='color: green;'>Kolumna '<strong>{$columnName}</strong>' została pomyślnie dodana do tabeli '<strong>{$tableName}</strong>'.</p>";
    } else {
        echo "<p style='color: red;'>Błąd podczas dodawania kolumny '<strong>{$columnName}</strong>' do tabeli '<strong>{$tableName}</strong>': " . $conn->error . "</p>";
    }
}

$conn->close();

echo "<hr><p><em>Skrypt zakończył działanie. Możesz teraz usunąć ten plik (update_database_schema.php) z serwera lub pozostawić go do przyszłego użytku (nie zaszkodzi, jeśli zostanie uruchomiony ponownie).</em></p>";

?>
