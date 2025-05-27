<?php
require_once 'config.php';

try {
    // Read the SQL file
    $sql = file_get_contents('update_badania_structure.sql');
    
    // Execute the SQL
    if ($conn->multi_query($sql)) {
        echo "Successfully updated badania table structure.";
    } else {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?> 