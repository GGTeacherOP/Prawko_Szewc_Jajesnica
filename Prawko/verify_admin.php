<?php
require_once 'config.php';

try {
    // Check if admin user exists
    $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE email = ?");
    $email = 'admin1';
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "Admin user does not exist. Creating...<br>";
        
        // Create admin user
        $haslo = password_hash('admin1', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO uzytkownicy (email, haslo, imie, nazwisko, rola, telefon, kategoria_prawa_jazdy) VALUES (?, ?, 'Admin', 'System', 'admin', '000000000', 'B')");
        $stmt->bind_param("ss", $email, $haslo);
        
        if ($stmt->execute()) {
            echo "Admin user created successfully.<br>";
        } else {
            echo "Error creating admin user: " . $stmt->error . "<br>";
        }
    } else {
        $user = $result->fetch_assoc();
        echo "Admin user exists:<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Role: " . $user['rola'] . "<br>";
        
        // Verify password
        if (password_verify('admin1', $user['haslo'])) {
            echo "Password verification successful.<br>";
        } else {
            echo "Password verification failed. Updating password...<br>";
            
            // Update password
            $haslo = password_hash('admin1', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE uzytkownicy SET haslo = ? WHERE email = ?");
            $stmt->bind_param("ss", $haslo, $email);
            
            if ($stmt->execute()) {
                echo "Password updated successfully.<br>";
            } else {
                echo "Error updating password: " . $stmt->error . "<br>";
            }
        }
    }
    
    // Display table structure
    echo "<br>Table structure:<br>";
    $result = $conn->query("DESCRIBE uzytkownicy");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

$conn->close();
?> 