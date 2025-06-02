-- SQL script to create the database and tables for the driving school

-- Utworzenie bazy danych
CREATE DATABASE IF NOT EXISTS szkolajazdydb;
USE szkolajazdydb;

-- Usunięcie starej tabeli instruktorów jeśli istnieje
DROP TABLE IF EXISTS instruktorzy;

-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS uzytkownicy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    data_urodzenia DATE NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
    rola ENUM('kursant', 'instruktor', 'admin') NOT NULL DEFAULT 'kursant',
    data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela pracowników (dawniej instruktorzy)
CREATE TABLE IF NOT EXISTS pracownicy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    kategorie_uprawnien SET('A', 'B', 'C', 'D') NOT NULL,
    rola ENUM('instruktor', 'ksiegowy', 'admin') NOT NULL DEFAULT 'instruktor'
);

-- Tabela kursów
CREATE TABLE IF NOT EXISTS kursy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL,
    kategoria ENUM('Prawo Jazdy', 'Instruktorzy', 'Kierowcy Zawodowi', 'Operatorzy Maszyn') NOT NULL,
    opis TEXT,
    cena DECIMAL(10,2) NOT NULL
);

-- Tabela zapisów
CREATE TABLE IF NOT EXISTS zapisy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_zapisu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący',
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Tabela pojazdów
CREATE TABLE IF NOT EXISTS pojazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    rok_produkcji INT(4) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
    stan_techniczny ENUM('Dobry', 'Średni', 'Do Naprawy') DEFAULT 'Dobry'
);

-- Tabela badań lekarskich
CREATE TABLE IF NOT EXISTS badania (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    data_badania DATE NOT NULL,
    typ ENUM('Podstawowe', 'Rozszerzone', 'Psychologiczne') NOT NULL DEFAULT 'Podstawowe',
    wynik ENUM('Pozytywny', 'Negatywny') NOT NULL,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący',
    waznosc_do DATE,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id)
);

-- Tabela płatności
CREATE TABLE IF NOT EXISTS platnosci (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    kwota DECIMAL(10,2) NOT NULL,
    data_platnosci TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Opłacony', 'Anulowany') DEFAULT 'Oczekujący',
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Tabela terminów
CREATE TABLE IF NOT EXISTS terminy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kurs_id INT(11),
    instruktor_id INT(11),
    data_rozpoczecia DATETIME NOT NULL,
    data_zakonczenia DATETIME NOT NULL,
    max_uczestnikow INT(11) NOT NULL,
    FOREIGN KEY (kurs_id) REFERENCES kursy(id),
    FOREIGN KEY (instruktor_id) REFERENCES pracownicy(id)
);

-- Tabela certyfikatów
CREATE TABLE IF NOT EXISTS certyfikaty (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_uzyskania DATE NOT NULL,
    numer_certyfikatu VARCHAR(50) UNIQUE NOT NULL,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Tabela opinii
CREATE TABLE IF NOT EXISTS opinie (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    ocena INT(1) NOT NULL CHECK (ocena BETWEEN 1 AND 5),
    komentarz TEXT,
    data_opinii TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Tabela dostępności instruktorów
CREATE TABLE IF NOT EXISTS dostepnosc_instruktorow (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    instruktor_id INT(11) NOT NULL,
    data DATE NOT NULL,
    godzina_od TIME NOT NULL,
    godzina_do TIME NOT NULL,
    status ENUM('Dostępny', 'Zajęty', 'Niedostępny') DEFAULT 'Dostępny',
    FOREIGN KEY (instruktor_id) REFERENCES pracownicy(id),
    UNIQUE KEY unique_availability (instruktor_id, data, godzina_od, godzina_do)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela jazd
CREATE TABLE IF NOT EXISTS jazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kursant_id INT(11) NOT NULL,
    instruktor_id INT(11) NOT NULL,
    kurs_id INT(11) NOT NULL,
    data_jazdy DATETIME NOT NULL,
    status ENUM('Zaplanowana', 'Zakończona', 'Anulowana') DEFAULT 'Zaplanowana',
    ocena INT(1) DEFAULT NULL CHECK (ocena BETWEEN 1 AND 5),
    komentarz TEXT,
    data_utworzenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kursant_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (instruktor_id) REFERENCES pracownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela resetowania haseł
CREATE TABLE IF NOT EXISTS reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiry DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wyłączenie sprawdzania kluczy obcych
SET FOREIGN_KEY_CHECKS = 0;

-- Przykładowi użytkownicy (hasła jawne!)
INSERT INTO uzytkownicy (login, imie, nazwisko, email, telefon, data_urodzenia, haslo, kategoria_prawa_jazdy, rola) VALUES
('jan_kowalski', 'Jan', 'Kowalski', 'jan.kowalski@email.com', '123456789', '1990-05-15', 'haslo123', 'B', 'kursant'),
('anna_nowak', 'Anna', 'Nowak', 'anna.nowak@email.com', '987654321', '1995-08-22', '123', 'A', 'kursant'),
('piotr_wisniewski', 'Piotr', 'Wiśniewski', 'piotr.wisniewski@email.com', '555666777', '1988-03-10', 'haslo321', 'C', 'kursant'),
('maria_dabrowska', 'Maria', 'Dąbrowska', 'maria.dabrowska@email.com', '111222333', '1992-11-30', 'haslo456', 'B', 'kursant'),
('tomasz_lewandowski', 'Tomasz', 'Lewandowski', 'tomasz.lew@email.com', '444555666', '1993-07-25', 'haslo789', 'D', 'kursant');

-- Przykładowi pracownicy
INSERT INTO pracownicy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien, rola) VALUES
('Anna', 'Księgowa', 'ksiegowa@firma.pl', '123456789', 'haslo123', '', 'ksiegowy'),
('Adam', 'Admin', 'admin@firma.pl', '987654321', 'admin123', '', 'admin'),
('Marek', 'Kowalczyk', 'marek.kowalczyk@szkola.pl', '111333555', 'instruktor1', 'A,B', 'instruktor'),
('Barbara', 'Wojcik', 'barbara.wojcik@szkola.pl', '222444666', 'instruktor2', 'B,C', 'instruktor'),
('Krzysztof', 'Nowicki', 'krzysztof.nowicki@szkola.pl', '333555777', 'instruktor3', 'A,B,C,D', 'instruktor');

-- Przykładowe kursy
INSERT INTO kursy (nazwa, kategoria, opis, cena) VALUES
('Kurs na prawo jazdy kat. B', 'Prawo Jazdy', 'Podstawowy kurs na prawo jazdy kategorii B', 2500.00),
('Kurs na prawo jazdy kat. A', 'Prawo Jazdy', 'Kurs na motocykl', 2000.00),
('Kurs na prawo jazdy kat. C', 'Kierowcy Zawodowi', 'Kurs dla kierowców zawodowych', 3500.00),
('Kurs na prawo jazdy kat. D', 'Kierowcy Zawodowi', 'Kurs dla kierowców autobusów', 4000.00),
('Kurs instruktorski', 'Instruktorzy', 'Kurs dla przyszłych instruktorów', 5000.00),
('Kurs Kierowcy Zawodowego', 'Kierowcy Zawodowi', 'Kompleksowy kurs dla kierowców zawodowych', 3000.00),
('Kwalifikacja Okresowa', 'Kierowcy Zawodowi', 'Kwalifikacja okresowa dla kierowców zawodowych', 1500.00),
('Kurs ADR', 'Kierowcy Zawodowi', 'Kurs przewozu materiałów niebezpiecznych', 2000.00),
('Kurs Tachografu', 'Kierowcy Zawodowi', 'Kurs obsługi tachografu cyfrowego', 800.00),
('Kurs Przewozu Osób', 'Kierowcy Zawodowi', 'Kurs przewozu osób', 1200.00),
('Kurs Przewozu Rzeczy', 'Kierowcy Zawodowi', 'Kurs przewozu rzeczy', 1000.00);

-- Wstawianie przykładowych pojazdów
INSERT INTO pojazdy (marka, model, rok_produkcji, kategoria_prawa_jazdy, stan_techniczny) VALUES
('Toyota', 'Yaris', 2020, 'B', 'Dobry'),
('Honda', 'CBR500', 2021, 'A', 'Dobry'),
('Scania', 'R450', 2019, 'C', 'Dobry'),
('Mercedes', 'Sprinter', 2018, 'B', 'Średni'),
('Volvo', '9900', 2020, 'D', 'Dobry');

-- Wstawianie przykładowych zapisów na kursy
INSERT INTO zapisy (uzytkownik_id, kurs_id, status) VALUES
(1, 1, 'Zatwierdzony'),
(2, 2, 'Zatwierdzony');

-- Wstawianie przykładowych badań lekarskich
INSERT INTO badania (uzytkownik_id, data_badania, wynik, waznosc_do) VALUES
(1, '2023-01-15', 'Pozytywny', '2024-01-15'),
(2, '2023-02-20', 'Pozytywny', '2024-02-20');

-- Wstawianie przykładowych płatności
INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) VALUES
(1, 1, 2500.00, 'Opłacony'),
(2, 2, 2000.00, 'Opłacony');

-- Wstawianie przykładowych certyfikatów
INSERT INTO certyfikaty (uzytkownik_id, kurs_id, data_uzyskania, numer_certyfikatu) VALUES
(1, 1, '2023-06-15', 'CERT/2023/001'),
(2, 2, '2023-07-20', 'CERT/2023/002');

-- Wstawianie przykładowych opinii
INSERT INTO opinie (uzytkownik_id, kurs_id, ocena, komentarz) VALUES
(1, 1, 5, 'Świetny kurs, profesjonalni instruktorzy'),
(2, 2, 4, 'Dobry kurs, pomocni instruktorzy');

-- Przykładowe dane dla dostępności instruktorów
INSERT INTO dostepnosc_instruktorow (instruktor_id, data, godzina_od, godzina_do) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', '16:00:00'),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '18:00:00');

-- Przykładowe dane dla jazd
INSERT INTO jazdy (kursant_id, instruktor_id, kurs_id, data_jazdy, status, ocena, komentarz) VALUES
(1, 1, 1, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Zaplanowana', NULL, NULL),
(2, 2, 2, DATE_ADD(NOW(), INTERVAL -1 DAY), 'Zakończona', 5, 'Bardzo dobra jazda');

-- Włączenie sprawdzania kluczy obcych
SET FOREIGN_KEY_CHECKS = 1;
