<?php
require_once 'config.php';

// Execute SQL queries
$sql = file_get_contents('insert_instructors_vehicles.sql');
$queries = explode(';', $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === FALSE) {
            die("Error executing query: " . $conn->error);
        }
    }
}

echo "Instructors and vehicles updated successfully!";
?> 