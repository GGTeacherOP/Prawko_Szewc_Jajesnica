-- Drop the existing table
DROP TABLE IF EXISTS platnosci;

-- Recreate the table with opis column
CREATE TABLE platnosci (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11) NULL,
    kwota DECIMAL(10,2) NOT NULL,
    data_platnosci TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Opłacony', 'Anulowany') DEFAULT 'Oczekujący',
    opis VARCHAR(255),
    badanie_id INT NULL,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id),
    FOREIGN KEY (badanie_id) REFERENCES badania(id) ON DELETE SET NULL
);

-- Update existing records to ensure compatibility
UPDATE platnosci SET badanie_id = NULL WHERE badanie_id IS NOT NULL; 