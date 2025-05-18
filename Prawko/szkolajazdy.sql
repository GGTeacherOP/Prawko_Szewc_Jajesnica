-- SQL script to create the database and tables for the driving school

-- Create database
CREATE DATABASE IF NOT EXISTS szkolajazdydb;
USE szkolajazdydb;

-- Users table
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

-- Courses table
CREATE TABLE IF NOT EXISTS kursy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL,
    kategoria ENUM('Prawo Jazdy', 'Instruktorzy', 'Kierowcy Zawodowi', 'Operatorzy Maszyn') NOT NULL,
    opis TEXT,
    cena DECIMAL(10,2) NOT NULL
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS zapisy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_zapisu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący',
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Instructors table
CREATE TABLE IF NOT EXISTS instruktorzy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    kategorie_uprawnien SET('A', 'B', 'C', 'D') NOT NULL
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS pojazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    rok_produkcji INT(4) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
    stan_techniczny ENUM('Dobry', 'Średni', 'Do Naprawy') DEFAULT 'Dobry'
);

-- Medical examinations table
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

-- Payments table
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

-- Schedules table
CREATE TABLE IF NOT EXISTS terminy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kurs_id INT(11),
    instruktor_id INT(11),
    data_rozpoczecia DATETIME NOT NULL,
    data_zakonczenia DATETIME NOT NULL,
    max_uczestnikow INT(11) NOT NULL,
    FOREIGN KEY (kurs_id) REFERENCES kursy(id),
    FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id)
);

-- Certificates table
CREATE TABLE IF NOT EXISTS certyfikaty (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11),
    kurs_id INT(11),
    data_uzyskania DATE NOT NULL,
    numer_certyfikatu VARCHAR(50) UNIQUE NOT NULL,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
);

-- Reviews table
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
    FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id),
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
    FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id),
    FOREIGN KEY (kurs_id) REFERENCES kursy(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create reset_tokens table
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

-- Wstawianie przykładowych użytkowników
INSERT INTO uzytkownicy (login, imie, nazwisko, email, telefon, data_urodzenia, haslo, kategoria_prawa_jazdy, rola) VALUES
('jan_kowalski', 'Jan', 'Kowalski', 'jan.kowalski@email.com', '123456789', '1990-05-15', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('anna_nowak', 'Anna', 'Nowak', 'anna.nowak@email.com', '987654321', '1995-08-22', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('piotr_wisniewski', 'Piotr', 'Wiśniewski', 'piotr.wisniewski@email.com', '555666777', '1988-03-10', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('maria_dabrowska', 'Maria', 'Dąbrowska', 'maria.dabrowska@email.com', '111222333', '1992-11-30', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('tomasz_lewandowski', 'Tomasz', 'Lewandowski', 'tomasz.lew@email.com', '444555666', '1993-07-25', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('karolina_wojcik', 'Karolina', 'Wójcik', 'karolina.wojcik@email.com', '666777888', '1994-02-15', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('michal_kaminski', 'Michał', 'Kamiński', 'michal.kaminski@email.com', '777888999', '1991-09-20', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('agnieszka_zielinska', 'Agnieszka', 'Zielińska', 'agnieszka.zielinska@email.com', '888999000', '1989-12-05', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('krzysztof_szymanski', 'Krzysztof', 'Szymański', 'krzysztof.szymanski@email.com', '999000111', '1987-06-30', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('monika_adamczyk', 'Monika', 'Adamczyk', 'monika.adamczyk@email.com', '000111222', '1996-04-12', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('pawel_kaczmarek', 'Paweł', 'Kaczmarek', 'pawel.kaczmarek@email.com', '111222333', '1993-11-08', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('aleksandra_piotrowska', 'Aleksandra', 'Piotrowska', 'aleksandra.piotrowska@email.com', '222333444', '1990-08-17', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('marcin_grabowski', 'Marcin', 'Grabowski', 'marcin.grabowski@email.com', '333444555', '1988-03-25', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('natalia_michalska', 'Natalia', 'Michalska', 'natalia.michalska@email.com', '444555666', '1995-01-14', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('damian_nowakowski', 'Damian', 'Nowakowski', 'damian.nowakowski@email.com', '555666777', '1992-07-19', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('weronika_krol', 'Weronika', 'Król', 'weronika.krol@email.com', '666777888', '1994-05-23', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('lukasz_wrobel', 'Łukasz', 'Wróbel', 'lukasz.wrobel@email.com', '777888999', '1991-12-30', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('magdalena_kozlowska', 'Magdalena', 'Kozłowska', 'magdalena.kozlowska@email.com', '888999000', '1989-09-11', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('rafal_jankowski', 'Rafał', 'Jankowski', 'rafal.jankowski@email.com', '999000111', '1993-04-05', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('ewelina_mazur', 'Ewelina', 'Mazur', 'ewelina.mazur@email.com', '000111222', '1996-02-28', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('bartosz_wojciechowski', 'Bartosz', 'Wojciechowski', 'bartosz.wojciechowski@email.com', '111222333', '1990-11-15', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('klaudia_kwiatkowska', 'Klaudia', 'Kwiatkowska', 'klaudia.kwiatkowska@email.com', '222333444', '1995-08-07', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('adrian_krawczyk', 'Adrian', 'Krawczyk', 'adrian.krawczyk@email.com', '333444555', '1992-03-20', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('sylwia_walczak', 'Sylwia', 'Walczak', 'sylwia.walczak@email.com', '444555666', '1988-06-25', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('kamil_olszewski', 'Kamil', 'Olszewski', 'kamil.olszewski@email.com', '555666777', '1994-01-30', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('patrycja_dudek', 'Patrycja', 'Dudek', 'patrycja.dudek@email.com', '666777888', '1991-10-12', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('mateusz_wieczorek', 'Mateusz', 'Wieczorek', 'mateusz.wieczorek@email.com', '777888999', '1993-07-08', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('dominika_adamska', 'Dominika', 'Adamska', 'dominika.adamska@email.com', '888999000', '1989-04-17', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('sebastian_sikora', 'Sebastian', 'Sikora', 'sebastian.sikora@email.com', '999000111', '1995-12-22', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('karolina_gorska', 'Karolina', 'Górska', 'karolina.gorska@email.com', '000111222', '1992-09-14', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('daniel_pawlowski', 'Daniel', 'Pawłowski', 'daniel.pawlowski@email.com', '111222333', '1990-05-19', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('julia_michalak', 'Julia', 'Michalak', 'julia.michalak@email.com', '222333444', '1994-02-24', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('przemyslaw_witkowski', 'Przemysław', 'Witkowski', 'przemyslaw.witkowski@email.com', '333444555', '1991-11-29', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('martyna_rutkowska', 'Martyna', 'Rutkowska', 'martyna.rutkowska@email.com', '444555666', '1993-08-03', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('konrad_zajac', 'Konrad', 'Zając', 'konrad.zajac@email.com', '555666777', '1988-03-08', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('nina_stepien', 'Nina', 'Stępień', 'nina.stepien@email.com', '666777888', '1996-01-13', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('filip_szczepanski', 'Filip', 'Szczepański', 'filip.szczepanski@email.com', '777888999', '1992-10-18', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('oliwia_karpinska', 'Oliwia', 'Karpińska', 'oliwia.karpinska@email.com', '888999000', '1995-07-23', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('hubert_baran', 'Hubert', 'Baran', 'hubert.baran@email.com', '999000111', '1990-04-28', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('wiktoria_czarnecka', 'Wiktoria', 'Czarnecka', 'wiktoria.czarnecka@email.com', '000111222', '1993-01-02', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('oskar_sobczak', 'Oskar', 'Sobczak', 'oskar.sobczak@email.com', '111222333', '1989-10-07', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('alicja_polak', 'Alicja', 'Polak', 'alicja.polak@email.com', '222333444', '1994-07-12', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('igor_wasilewski', 'Igor', 'Wasilewski', 'igor.wasilewski@email.com', '333444555', '1991-04-17', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('maja_sikorska', 'Maja', 'Sikorska', 'maja.sikorska@email.com', '444555666', '1996-01-22', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('alan_maciejewski', 'Alan', 'Maciejewski', 'alan.maciejewski@email.com', '555666777', '1992-10-27', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('kornelia_wysocka', 'Kornelia', 'Wysocka', 'kornelia.wysocka@email.com', '666777888', '1988-07-01', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('marcel_jakubowski', 'Marcel', 'Jakubowski', 'marcel.jakubowski@email.com', '777888999', '1993-04-06', '$2y$10$abcdefghijklmnopqrstuv', 'C', 'kursant'),
('lidia_sadowska', 'Lidia', 'Sadowska', 'lidia.sadowska@email.com', '888999000', '1995-01-11', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('kacper_zawadzki', 'Kacper', 'Zawadzki', 'kacper.zawadzki@email.com', '999000111', '1990-10-16', '$2y$10$abcdefghijklmnopqrstuv', 'D', 'kursant'),
('rozalia_pietrzak', 'Rozalia', 'Pietrzak', 'rozalia.pietrzak@email.com', '000111222', '1994-07-21', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant'),
('nikodem_tomczak', 'Nikodem', 'Tomczak', 'nikodem.tomczak@email.com', '111222333', '1991-04-26', '$2y$10$abcdefghijklmnopqrstuv', 'A', 'kursant'),
('sandra_jasinska', 'Sandra', 'Jasińska', 'sandra.jasinska@email.com', '222333444', '1989-01-31', '$2y$10$abcdefghijklmnopqrstuv', 'B', 'kursant');

-- Wstawianie przykładowych kursów
INSERT INTO kursy (nazwa, kategoria, opis, cena) VALUES
('Kurs na prawo jazdy kat. B', 'Prawo Jazdy', 'Podstawowy kurs na prawo jazdy kategorii B', 2500.00),
('Kurs na prawo jazdy kat. A', 'Prawo Jazdy', 'Kurs na motocykl', 2000.00),
('Kurs na prawo jazdy kat. C', 'Kierowcy Zawodowi', 'Kurs dla kierowców zawodowych', 3500.00),
('Kurs na prawo jazdy kat. D', 'Kierowcy Zawodowi', 'Kurs dla kierowców autobusów', 4000.00),
('Kurs instruktorski', 'Instruktorzy', 'Kurs dla przyszłych instruktorów', 5000.00);

-- Wstawianie przykładowych instruktorów
INSERT INTO instruktorzy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien) VALUES
('Marek', 'Kowalczyk', 'marek.kowalczyk@szkola.pl', '111333555', '$2y$10$abcdefghijklmnopqrstuv', 'A,B'),
('Barbara', 'Wojcik', 'barbara.wojcik@szkola.pl', '222444666', '$2y$10$abcdefghijklmnopqrstuv', 'B,C'),
('Krzysztof', 'Nowicki', 'krzysztof.nowicki@szkola.pl', '333555777', '$2y$10$abcdefghijklmnopqrstuv', 'A,B,C,D'),
('Ewa', 'Kamińska', 'ewa.kaminska@szkola.pl', '444666888', '$2y$10$abcdefghijklmnopqrstuv', 'B'),
('Adam', 'Zieliński', 'adam.zielinski@szkola.pl', '555777999', '$2y$10$abcdefghijklmnopqrstuv', 'C,D'),
('Dorota', 'Szymańska', 'dorota.szymanska@szkola.pl', '666888000', '$2y$10$abcdefghijklmnopqrstuv', 'A,B'),
('Robert', 'Dąbrowski', 'robert.dabrowski@szkola.pl', '777999111', '$2y$10$abcdefghijklmnopqrstuv', 'B,C,D'),
('Małgorzata', 'Kozłowska', 'malgorzata.kozlowska@szkola.pl', '888000222', '$2y$10$abcdefghijklmnopqrstuv', 'A,B'),
('Tomasz', 'Jankowski', 'tomasz.jankowski@szkola.pl', '999111333', '$2y$10$abcdefghijklmnopqrstuv', 'B,C'),
('Anna', 'Wojciechowska', 'anna.wojciechowska@szkola.pl', '000222444', '$2y$10$abcdefghijklmnopqrstuv', 'A,B,C,D');

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
(2, 2, 'Zatwierdzony'),
(3, 3, 'Oczekujący'),
(4, 1, 'Zatwierdzony'),
(5, 4, 'Zatwierdzony');

-- Wstawianie przykładowych badań lekarskich
INSERT INTO badania (uzytkownik_id, data_badania, wynik, waznosc_do) VALUES
(1, '2023-01-15', 'Pozytywny', '2024-01-15'),
(2, '2023-02-20', 'Pozytywny', '2024-02-20'),
(3, '2023-03-10', 'Pozytywny', '2024-03-10'),
(4, '2023-04-05', 'Pozytywny', '2024-04-05'),
(5, '2023-05-01', 'Pozytywny', '2024-05-01');

-- Wstawianie przykładowych płatności
INSERT INTO platnosci (uzytkownik_id, kurs_id, kwota, status) VALUES
(1, 1, 2500.00, 'Opłacony'),
(2, 2, 2000.00, 'Opłacony'),
(3, 3, 3500.00, 'Oczekujący'),
(4, 1, 2500.00, 'Opłacony'),
(5, 4, 4000.00, 'Opłacony');

-- Wstawianie przykładowych certyfikatów
INSERT INTO certyfikaty (uzytkownik_id, kurs_id, data_uzyskania, numer_certyfikatu) VALUES
(1, 1, '2023-06-15', 'CERT/2023/001'),
(2, 2, '2023-07-20', 'CERT/2023/002'),
(4, 1, '2023-08-10', 'CERT/2023/003'),
(5, 4, '2023-09-05', 'CERT/2023/004');

-- Wstawianie przykładowych opinii
INSERT INTO opinie (uzytkownik_id, kurs_id, ocena, komentarz) VALUES
(1, 1, 5, 'Świetny kurs, profesjonalni instruktorzy'),
(2, 2, 4, 'Dobry kurs, pomocni instruktorzy'),
(4, 1, 5, 'Bardzo dobra organizacja kursu'),
(5, 4, 4, 'Profesjonalne podejście do kursantów');

-- Przykładowe dane dla dostępności instruktorów
INSERT INTO dostepnosc_instruktorow (instruktor_id, data, godzina_od, godzina_do) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', '16:00:00'),
(1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00'),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '18:00:00'),
(2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', '16:00:00'),
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '07:00:00', '15:00:00'),
(3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00');

-- Przykładowe dane dla jazd
INSERT INTO jazdy (kursant_id, instruktor_id, kurs_id, data_jazdy, status, ocena, komentarz) VALUES
(1, 1, 1, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Zaplanowana', NULL, NULL),
(2, 2, 2, DATE_ADD(NOW(), INTERVAL -1 DAY), 'Zakończona', 5, 'Bardzo dobra jazda'),
(3, 3, 3, DATE_ADD(NOW(), INTERVAL -2 DAY), 'Zakończona', 4, 'Dobre postępy'),
(1, 1, 1, DATE_ADD(NOW(), INTERVAL -3 DAY), 'Zakończona', 5, 'Świetne opanowanie pojazdu'),
(2, 2, 2, DATE_ADD(NOW(), INTERVAL 2 DAY), 'Zaplanowana', NULL, NULL),
(3, 3, 3, DATE_ADD(NOW(), INTERVAL -1 DAY), 'Anulowana', NULL, 'Choroba kursanta');

-- Włączenie sprawdzania kluczy obcych
SET FOREIGN_KEY_CHECKS = 1;
