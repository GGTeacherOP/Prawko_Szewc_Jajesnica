-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop and recreate instructors table
DROP TABLE IF EXISTS instruktorzy;
CREATE TABLE instruktorzy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    kategorie_uprawnien VARCHAR(100) NOT NULL
);

-- Insert sample instructors
INSERT INTO instruktorzy (imie, nazwisko, email, telefon, kategorie_uprawnien) VALUES
('Jan', 'Kowalski', 'jan.kowalski@example.com', '123456789', 'A,B'),
('Anna', 'Nowak', 'anna.nowak@example.com', '987654321', 'B,C'),
('Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', '555666777', 'C,D');

-- Drop and recreate vehicles table
DROP TABLE IF EXISTS pojazdy;
CREATE TABLE pojazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    numer_rejestracyjny VARCHAR(20) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
    stan_techniczny ENUM('Dobry', 'Średni', 'Do Naprawy') DEFAULT 'Dobry'
);

-- Insert sample vehicles
INSERT INTO pojazdy (marka, model, numer_rejestracyjny, kategoria_prawa_jazdy, stan_techniczny) VALUES
('Honda', 'CBR500', 'SBI12345', 'A', 'Dobry'),
('Toyota', 'Yaris', 'SBI54321', 'B', 'Dobry'),
('Scania', 'R450', 'SBI98765', 'C', 'Dobry'),
('Mercedes', 'Tourismo', 'SBI45678', 'D', 'Dobry');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1; 