<?php
require_once 'config.php';

try {
    // Add badanie_id column
    $sql = "ALTER TABLE platnosci
            ADD COLUMN badanie_id INT NULL,
            ADD FOREIGN KEY (badanie_id) REFERENCES badania(id) ON DELETE SET NULL";
    
    if ($conn->query($sql)) {
        echo "Successfully added badanie_id column to platnosci table.<br>";
    } else {
        echo "Error adding badanie_id column: " . $conn->error . "<br>";
    }

    // Update existing records
    $sql = "UPDATE platnosci SET badanie_id = NULL WHERE badanie_id IS NOT NULL";
    if ($conn->query($sql)) {
        echo "Successfully updated existing records.<br>";
    } else {
        echo "Error updating records: " . $conn->error . "<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

$conn->close();
?> 