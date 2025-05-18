<?php
require_once 'config.php';

try {
    // Disable autocommit to start transaction
    $conn->autocommit(FALSE);
    
    // Read SQL file
    $sql = file_get_contents('add_missing_columns.sql');
    
    // Split SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement separately
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if (!$conn->query($statement)) {
                throw new Exception("Error executing statement: " . $conn->error . "\nStatement: " . $statement);
            }
        }
    }
    
    // If we got here, commit the changes
    $conn->commit();
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f0f8ff; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #2c3e50;'>Kolumny zostały dodane pomyślnie!</h2>";
    echo "<p style='color: #34495e;'>Dodano brakujące kolumny 'login' i 'data_urodzenia' do tabeli uzytkownicy.</p>";
    echo "<p style='color: #34495e; margin-top: 20px;'>Możesz teraz:</p>";
    echo "<ul style='color: #34495e;'>";
    echo "<li>Sprawdzić <a href='check_database.php' style='color: #3498db; text-decoration: none;'>strukturę bazy danych</a></li>";
    echo "<li>Wrócić do <a href='register.php' style='color: #3498db; text-decoration: none;'>strony rejestracji</a></li>";
    echo "<li>Przejść do <a href='login.php' style='color: #3498db; text-decoration: none;'>strony logowania</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    // If there was an error, roll back the changes
    $conn->rollback();
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #ffe6e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #c0392b;'>Wystąpił błąd podczas dodawania kolumn</h2>";
    echo "<p style='color: #e74c3c;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: #34495e; margin-top: 20px;'>Spróbuj:</p>";
    echo "<ul style='color: #34495e;'>";
    echo "<li>Sprawdzić <a href='check_database.php' style='color: #3498db; text-decoration: none;'>aktualną strukturę bazy danych</a></li>";
    echo "<li>Skontaktować się z administratorem jeśli problem się powtarza</li>";
    echo "</ul>";
    echo "</div>";
}

// Re-enable autocommit
$conn->autocommit(TRUE);
$conn->close();
?> 