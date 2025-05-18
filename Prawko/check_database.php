<?php
require_once 'config.php';

function checkTableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

function checkColumnExists($conn, $tableName, $columnName) {
    $result = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
    return $result->num_rows > 0;
}

function displayTableStructure($conn, $tableName) {
    $result = $conn->query("DESCRIBE `$tableName`");
    echo "<h3>Structure of table '$tableName':</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td style='padding: 5px;'>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// HTML header and styling
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Structure Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .container { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>Database Structure Check Report</h1>";

// Check connection
if ($conn->connect_error) {
    die("<p class='error'>Connection failed: " . $conn->connect_error . "</p>");
}

// List of required tables
$requiredTables = [
    'uzytkownicy',
    'kursy',
    'zapisy',
    'badania',
    'platnosci',
    'certyfikaty',
    'opinie'
];

// Check each table
foreach ($requiredTables as $table) {
    if (checkTableExists($conn, $table)) {
        echo "<p class='success'>✓ Table '$table' exists</p>";
        displayTableStructure($conn, $table);
    } else {
        echo "<p class='error'>✗ Table '$table' is missing!</p>";
    }
}

// Check specific columns in uzytkownicy table
if (checkTableExists($conn, 'uzytkownicy')) {
    $requiredColumns = ['id', 'login', 'email', 'haslo', 'imie', 'nazwisko', 'telefon', 'data_urodzenia', 'kategoria_prawa_jazdy'];
    foreach ($requiredColumns as $column) {
        if (checkColumnExists($conn, 'uzytkownicy', $column)) {
            echo "<p class='success'>✓ Column '$column' exists in uzytkownicy table</p>";
        } else {
            echo "<p class='error'>✗ Column '$column' is missing in uzytkownicy table!</p>";
        }
    }
}

// Check foreign key relationships
$relationships = [
    ['zapisy', 'uzytkownik_id', 'uzytkownicy', 'id'],
    ['zapisy', 'kurs_id', 'kursy', 'id'],
    ['badania', 'uzytkownik_id', 'uzytkownicy', 'id'],
    ['platnosci', 'uzytkownik_id', 'uzytkownicy', 'id'],
    ['platnosci', 'kurs_id', 'kursy', 'id'],
    ['certyfikaty', 'uzytkownik_id', 'uzytkownicy', 'id'],
    ['certyfikaty', 'kurs_id', 'kursy', 'id'],
    ['opinie', 'uzytkownik_id', 'uzytkownicy', 'id'],
    ['opinie', 'kurs_id', 'kursy', 'id']
];

echo "<h2>Foreign Key Relationships</h2>";
foreach ($relationships as $rel) {
    $sql = "SELECT 
        COUNT(*) as cnt
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = '{$rel[0]}'
        AND COLUMN_NAME = '{$rel[1]}'
        AND REFERENCED_TABLE_NAME = '{$rel[2]}'
        AND REFERENCED_COLUMN_NAME = '{$rel[3]}'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['cnt'] > 0) {
        echo "<p class='success'>✓ Foreign key relationship exists: {$rel[0]}.{$rel[1]} -> {$rel[2]}.{$rel[3]}</p>";
    } else {
        echo "<p class='error'>✗ Missing foreign key relationship: {$rel[0]}.{$rel[1]} -> {$rel[2]}.{$rel[3]}</p>";
    }
}

// Add a button to run the database update if needed
echo "<div style='margin-top: 30px; padding: 20px; background-color: #f5f5f5; border-radius: 5px;'>";
echo "<h2>Database Update Options</h2>";

// Show different update options based on what's missing
$missing_columns = false;
if (checkTableExists($conn, 'uzytkownicy')) {
    $required_columns = ['login', 'data_urodzenia'];
    foreach ($required_columns as $column) {
        if (!checkColumnExists($conn, 'uzytkownicy', $column)) {
            $missing_columns = true;
            break;
        }
    }
}

if ($missing_columns) {
    echo "<p>Wykryto brakujące kolumny w tabeli uzytkownicy. Możesz je dodać klikając poniższy przycisk:</p>";
    echo "<form action='add_missing_columns.php' method='post' style='margin: 20px 0;'>";
    echo "<input type='submit' value='Dodaj brakujące kolumny' style='padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;'>";
    echo "</form>";
}

echo "<p>Jeśli potrzebujesz przebudować całą strukturę bazy danych, użyj poniższego przycisku:</p>";
echo "<form action='update_database.php' method='post' style='margin-top: 20px;'>";
echo "<input type='submit' value='Przebuduj całą bazę danych' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;'>";
echo "</form>";
echo "</div>";

echo "</div></body></html>";

$conn->close();
?> 