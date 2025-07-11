<?php
require_once 'config.php';

try {
    // Check if badanie_id column exists
    $check_sql = "SHOW COLUMNS FROM platnosci LIKE 'badanie_id'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows == 0) {
        // Add badanie_id column
        $sql = "ALTER TABLE platnosci 
                ADD COLUMN badanie_id INT(11) NULL";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error adding badanie_id column: " . $conn->error);
        }
        echo "Successfully added badanie_id column to platnosci table.<br>";
    } else {
        echo "badanie_id column already exists.<br>";
    }

    // Check if foreign key exists
    $check_fk_sql = "SELECT * FROM information_schema.KEY_COLUMN_USAGE 
                     WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'platnosci'
                     AND COLUMN_NAME = 'badanie_id'
                     AND REFERENCED_TABLE_NAME = 'badania'";
    
    $result = $conn->query($check_fk_sql);
    
    if ($result->num_rows == 0) {
        // Add foreign key
        $sql = "ALTER TABLE platnosci 
                ADD FOREIGN KEY (badanie_id) REFERENCES badania(id)";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error adding foreign key: " . $conn->error);
        }
        echo "Successfully added foreign key constraint.<br>";
    } else {
        echo "Foreign key constraint already exists.<br>";
    }

    // Update existing records
    $update_sql = "UPDATE platnosci SET badanie_id = NULL WHERE badanie_id IS NULL";
    if (!$conn->query($update_sql)) {
        throw new Exception("Error updating records: " . $conn->error);
    }
    echo "Successfully updated existing records.<br>";

    echo "Database update completed successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?> 