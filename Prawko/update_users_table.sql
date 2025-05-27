-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing foreign keys first
ALTER TABLE zapisy DROP FOREIGN KEY IF EXISTS zapisy_uzytkownik_id_fk;
ALTER TABLE zapisy DROP FOREIGN KEY IF EXISTS zapisy_kurs_id_fk;
ALTER TABLE badania DROP FOREIGN KEY IF EXISTS badania_uzytkownik_id_fk;
ALTER TABLE platnosci DROP FOREIGN KEY IF EXISTS platnosci_uzytkownik_id_fk;
ALTER TABLE platnosci DROP FOREIGN KEY IF EXISTS platnosci_kurs_id_fk;
ALTER TABLE certyfikaty DROP FOREIGN KEY IF EXISTS certyfikaty_uzytkownik_id_fk;
ALTER TABLE certyfikaty DROP FOREIGN KEY IF EXISTS certyfikaty_kurs_id_fk;
ALTER TABLE opinie DROP FOREIGN KEY IF EXISTS opinie_uzytkownik_id_fk;
ALTER TABLE opinie DROP FOREIGN KEY IF EXISTS opinie_kurs_id_fk;

-- Drop existing tables in correct order
DROP TABLE IF EXISTS opinie;
DROP TABLE IF EXISTS certyfikaty;
DROP TABLE IF EXISTS platnosci;
DROP TABLE IF EXISTS badania;
DROP TABLE IF EXISTS zapisy;
DROP TABLE IF EXISTS uzytkownicy;
DROP TABLE IF EXISTS kursy;

-- Create tables in correct order
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kursy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL,
    kategoria ENUM('Prawo Jazdy', 'Instruktorzy', 'Kierowcy Zawodowi', 'Operatorzy Maszyn') NOT NULL,
    opis TEXT,
    cena DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE zapisy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_zapisu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący',
    CONSTRAINT zapisy_uzytkownik_id_fk FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE,
    CONSTRAINT zapisy_kurs_id_fk FOREIGN KEY (kurs_id) REFERENCES kursy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE badania (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    data_badania DATE NOT NULL,
    wynik ENUM('Pozytywny', 'Negatywny') NOT NULL,
    waznosc_do DATE,
    CONSTRAINT badania_uzytkownik_id_fk FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE platnosci (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    kwota DECIMAL(10,2) NOT NULL,
    data_platnosci TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Opłacony', 'Anulowany') DEFAULT 'Oczekujący',
    CONSTRAINT platnosci_uzytkownik_id_fk FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE,
    CONSTRAINT platnosci_kurs_id_fk FOREIGN KEY (kurs_id) REFERENCES kursy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE certyfikaty (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_uzyskania DATE NOT NULL,
    numer_certyfikatu VARCHAR(50) UNIQUE NOT NULL,
    CONSTRAINT certyfikaty_uzytkownik_id_fk FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE,
    CONSTRAINT certyfikaty_kurs_id_fk FOREIGN KEY (kurs_id) REFERENCES kursy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE opinie (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    ocena INT(1) NOT NULL CHECK (ocena BETWEEN 1 AND 5),
    komentarz TEXT,
    data_opinii TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT opinie_uzytkownik_id_fk FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE,
    CONSTRAINT opinie_kurs_id_fk FOREIGN KEY (kurs_id) REFERENCES kursy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Aktualizacja istniejących rekordów (jeśli są)
UPDATE uzytkownicy SET login = CONCAT(imie, '_', id) WHERE login IS NULL; 