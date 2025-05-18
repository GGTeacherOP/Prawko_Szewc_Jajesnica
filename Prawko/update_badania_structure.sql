-- Drop the existing table
DROP TABLE IF EXISTS badania;

-- Create the table with correct structure
CREATE TABLE badania (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    data_badania DATETIME NOT NULL,
    wynik ENUM('Pozytywny', 'Negatywny', 'Oczekujący') DEFAULT 'Oczekujący',
    waznosc_do DATE,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Anulowany') DEFAULT 'Oczekujący',
    typ ENUM('Podstawowe', 'Zawodowe', 'Instruktorskie') NOT NULL,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE
); 