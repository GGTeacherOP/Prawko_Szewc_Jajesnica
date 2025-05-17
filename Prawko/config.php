<?php
// Database configuration
$host = 'localhost';
$db_username = 'root';
$db_password = '';
$database = 'szkolajazdydb';

// Create connection
$conn = new mysqli($host, $db_username, $db_password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create tables
function createTables($conn) {
    // Users table
    $users_table = "CREATE TABLE IF NOT EXISTS uzytkownicy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        imie VARCHAR(50) NOT NULL,
        nazwisko VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        data_urodzenia DATE NOT NULL,
        haslo VARCHAR(255) NOT NULL,
        kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
        data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    // Kursy table
    $kursy_table = "CREATE TABLE IF NOT EXISTS kursy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        nazwa VARCHAR(100) NOT NULL,
        kategoria ENUM('Prawo Jazdy', 'Instruktorzy', 'Kierowcy Zawodowi', 'Operatorzy Maszyn') NOT NULL,
        opis TEXT,
        cena DECIMAL(10,2) NOT NULL
    )";

    // Zapisy table
    $zapisy_table = "CREATE TABLE IF NOT EXISTS zapisy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        uzytkownik_id INT(11),
        kurs_id INT(11),
        data_zapisu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący',
        FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
        FOREIGN KEY (kurs_id) REFERENCES kursy(id)
    )";

    // Instruktorzy table
    $instruktorzy_table = "CREATE TABLE IF NOT EXISTS instruktorzy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        imie VARCHAR(50) NOT NULL,
        nazwisko VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        telefon VARCHAR(20) NOT NULL,
        kategorie_uprawnien SET('A', 'B', 'C', 'D') NOT NULL
    )";

    // Pojazdy table
    $pojazdy_table = "CREATE TABLE IF NOT EXISTS pojazdy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        marka VARCHAR(50) NOT NULL,
        model VARCHAR(50) NOT NULL,
        rok_produkcji INT(4) NOT NULL,
        kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
        stan_techniczny ENUM('Dobry', 'Średni', 'Do Naprawy') DEFAULT 'Dobry'
    )";

    // Badania table
    $badania_table = "CREATE TABLE IF NOT EXISTS badania (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        uzytkownik_id INT(11),
        data_badania DATE NOT NULL,
        wynik ENUM('Pozytywny', 'Negatywny') NOT NULL,
        waznosc_do DATE,
        FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id)
    )";

    // Platnosci table
    $platnosci_table = "CREATE TABLE IF NOT EXISTS platnosci (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        uzytkownik_id INT(11),
        kurs_id INT(11),
        kwota DECIMAL(10,2) NOT NULL,
        data_platnosci TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Oczekujący', 'Opłacony', 'Anulowany') DEFAULT 'Oczekujący',
        FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
        FOREIGN KEY (kurs_id) REFERENCES kursy(id)
    )";

    // Terminy table
    $terminy_table = "CREATE TABLE IF NOT EXISTS terminy (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        kurs_id INT(11),
        instruktor_id INT(11),
        data_rozpoczecia DATETIME NOT NULL,
        data_zakonczenia DATETIME NOT NULL,
        max_uczestnikow INT(11) NOT NULL,
        FOREIGN KEY (kurs_id) REFERENCES kursy(id),
        FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id)
    )";

    // Certyfikaty table
    $certyfikaty_table = "CREATE TABLE IF NOT EXISTS certyfikaty (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        uzytkownik_id INT(11),
        kurs_id INT(11),
        data_uzyskania DATE NOT NULL,
        numer_certyfikatu VARCHAR(50) UNIQUE NOT NULL,
        FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
        FOREIGN KEY (kurs_id) REFERENCES kursy(id)
    )";

    // Opinie table
    $opinie_table = "CREATE TABLE IF NOT EXISTS opinie (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        uzytkownik_id INT(11),
        kurs_id INT(11),
        ocena INT(1) NOT NULL CHECK (ocena BETWEEN 1 AND 5),
        komentarz TEXT,
        data_opinii TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
        FOREIGN KEY (kurs_id) REFERENCES kursy(id)
    )";

    // Execute table creation queries
    $tables = [
        $users_table, $kursy_table, $zapisy_table, 
        $instruktorzy_table, $pojazdy_table, $badania_table, 
        $platnosci_table, $terminy_table, $certyfikaty_table, 
        $opinie_table
    ];

    foreach ($tables as $table) {
        if ($conn->query($table) !== TRUE) {
            echo "Error creating table: " . $conn->error;
        }
    }
}

// Create tables
createTables($conn);
?>
