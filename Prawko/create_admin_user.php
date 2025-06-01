<?php
require_once 'config.php';

try {
    // Add admin user if not exists
    $sql = "INSERT INTO uzytkownicy (email, haslo, imie, nazwisko, rola, telefon, kategoria_prawa_jazdy)
            SELECT 'admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', 'admin', '000000000', 'B'
            WHERE NOT EXISTS (
                SELECT 1 FROM uzytkownicy WHERE email = 'admin1'
            )";
    
    if ($conn->query($sql)) {
        echo "Admin user created or already exists.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }

    // Update admin user if exists
    $sql = "UPDATE uzytkownicy 
            SET haslo = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                rola = 'admin'
            WHERE email = 'admin1'";
    
    if ($conn->query($sql)) {
        echo "Admin user updated successfully.<br>";
    } else {
        echo "Error updating admin user: " . $conn->error . "<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

$conn->close();
?> 