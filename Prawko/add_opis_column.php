<?php
require_once 'config.php';

try {
    // Read the SQL file
    $sql = file_get_contents('add_opis_column.sql');
    
    // Execute the SQL
    if ($conn->multi_query($sql)) {
        echo "Successfully added 'opis' column to platnosci table.";
    } else {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?> 