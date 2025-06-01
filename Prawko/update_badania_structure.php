<?php
require_once 'config.php';

try {
    // Read and execute SQL file
    $sql = file_get_contents('update_badania_structure.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "<h2>Wykonywanie aktualizacji bazy danych:</h2>";
    echo "<pre>";
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Wykonywanie: " . substr($statement, 0, 100) . "...\n";
            
            if (!$conn->query($statement)) {
                throw new Exception("Błąd wykonania zapytania: " . $conn->error . "\nZapytanie: " . $statement);
            }
            
            echo "✓ Wykonano pomyślnie\n\n";
        }
    }
    
    echo "</pre>";
    echo "<h3 style='color: green;'>Struktura bazy danych została zaktualizowana pomyślnie.</h3>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Wystąpił błąd:</h3>";
    echo "<pre style='color: red;'>" . $e->getMessage() . "</pre>";
}

$conn->close();
?> 