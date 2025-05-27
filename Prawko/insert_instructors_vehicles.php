<?php
require_once 'config.php';

try {
    // Read the SQL file
    $sql = file_get_contents('insert_instructors_vehicles.sql');
    
    // Split SQL into individual queries
    $queries = explode(';', $sql);
    
    // Execute each query separately
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === FALSE) {
                throw new Exception("Error executing query: " . $conn->error . "\nQuery: " . $query);
            }
        }
    }
    
    // Verify data was inserted
    $result = $conn->query("SELECT COUNT(*) as count FROM instruktorzy");
    $row = $result->fetch_assoc();
    echo "Successfully inserted " . $row['count'] . " instructors.\n";
    
    $result = $conn->query("SELECT COUNT(*) as count FROM pojazdy");
    $row = $result->fetch_assoc();
    echo "Successfully inserted " . $row['count'] . " vehicles.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?> 