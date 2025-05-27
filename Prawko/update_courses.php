<?php
require_once 'config.php';

try {
    // Start transaction
    $conn->autocommit(FALSE);
    
    // Clear existing courses
    $conn->query("DELETE FROM kursy");
    
    // Read and execute the course insertion SQL
    $sql = file_get_contents('insert_courses.sql');
    
    if (!$conn->multi_query($sql)) {
        throw new Exception("Error updating courses: " . $conn->error);
    }
    
    // Commit changes
    $conn->commit();
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f0f8ff; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #2c3e50;'>Kursy zostały zaktualizowane pomyślnie!</h2>";
    echo "<p style='color: #34495e;'>Wszystkie kursy zostały zaktualizowane w bazie danych.</p>";
    echo "<p style='color: #34495e; margin-top: 20px;'>Możesz teraz:</p>";
    echo "<ul style='color: #34495e;'>";
    echo "<li>Wrócić do <a href='kurs_prawa_jazdy.php' style='color: #3498db; text-decoration: none;'>strony kursów prawa jazdy</a></li>";
    echo "<li>Sprawdzić <a href='check_database.php' style='color: #3498db; text-decoration: none;'>strukturę bazy danych</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    // Roll back changes on error
    $conn->rollback();
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #ffe6e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #c0392b;'>Wystąpił błąd podczas aktualizacji kursów</h2>";
    echo "<p style='color: #e74c3c;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color: #34495e; margin-top: 20px;'>Spróbuj:</p>";
    echo "<ul style='color: #34495e;'>";
    echo "<li>Odświeżyć stronę i spróbować ponownie</li>";
    echo "<li>Sprawdzić <a href='check_database.php' style='color: #3498db; text-decoration: none;'>strukturę bazy danych</a></li>";
    echo "<li>Skontaktować się z administratorem jeśli problem się powtarza</li>";
    echo "</ul>";
    echo "</div>";
}

// Re-enable autocommit
$conn->autocommit(TRUE);
$conn->close();
?> 