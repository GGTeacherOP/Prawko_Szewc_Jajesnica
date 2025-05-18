-- Drop existing table
DROP TABLE IF EXISTS opinie;
DROP TABLE IF EXISTS certyfikaty;
DROP TABLE IF EXISTS terminy;
DROP TABLE IF EXISTS platnosci;
DROP TABLE IF EXISTS badania;
DROP TABLE IF EXISTS zapisy;
DROP TABLE IF EXISTS uzytkownicy;

-- Create updated users table
CREATE TABLE uzytkownicy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    data_urodzenia DATE NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
    data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 