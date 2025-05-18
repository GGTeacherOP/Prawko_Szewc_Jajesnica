<?php
require_once 'config.php';

try {
    // Wczytaj i wykonaj skrypt SQL
    $sql = file_get_contents('update_users_table.sql');
    
    if ($conn->multi_query($sql)) {
        do {
            // Pobierz wynik (jeśli jest)
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        if ($conn->error) {
            throw new Exception("Błąd podczas wykonywania zapytań: " . $conn->error);
        }
        
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f0f8ff; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
        echo "<h2 style='color: #2c3e50;'>Baza danych została zaktualizowana pomyślnie!</h2>";
        echo "<p style='color: #34495e;'>Wszystkie tabele zostały utworzone lub zaktualizowane.</p>";
        echo "<p style='color: #34495e; margin-top: 20px;'>Możesz teraz:</p>";
        echo "<ul style='color: #34495e;'>";
        echo "<li>Wrócić do <a href='register.php' style='color: #3498db; text-decoration: none;'>strony rejestracji</a></li>";
        echo "<li>Przejść do <a href='login.php' style='color: #3498db; text-decoration: none;'>strony logowania</a></li>";
        echo "<li>Wrócić na <a href='index.php' style='color: #3498db; text-decoration: none;'>stronę główną</a></li>";
        echo "</ul>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #ffe6e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #c0392b;'>Wystąpił błąd podczas aktualizacji bazy danych</h2>";
    echo "<p style='color: #e74c3c;'>" . $e->getMessage() . "</p>";
    echo "<p style='color: #34495e; margin-top: 20px;'>Spróbuj:</p>";
    echo "<ul style='color: #34495e;'>";
    echo "<li>Odświeżyć stronę</li>";
    echo "<li>Sprawdzić połączenie z bazą danych</li>";
    echo "<li>Skontaktować się z administratorem</li>";
    echo "</ul>";
    echo "</div>";
}

$conn->close();
?> 