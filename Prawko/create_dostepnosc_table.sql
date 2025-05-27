CREATE TABLE IF NOT EXISTS dostepnosc_instruktorow (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    instruktor_id INT(11) NOT NULL,
    data DATE NOT NULL,
    godzina_od TIME NOT NULL,
    godzina_do TIME NOT NULL,
    status ENUM('Dostępny', 'Zajęty', 'Niedostępny') DEFAULT 'Dostępny',
    FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id),
    UNIQUE KEY unique_availability (instruktor_id, data, godzina_od, godzina_do)
); 