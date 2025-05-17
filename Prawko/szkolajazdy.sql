-- SQL script to create the database and tables for the driving school

-- Create database
CREATE DATABASE IF NOT EXISTS szkolajazdydb;
USE szkolajazdydb;

-- Users table
CREATE TABLE IF NOT EXISTS uzytkownicy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    data_urodzenia DATE NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') NOT NULL,
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
    wynik ENUM('Pozytywny', 'Negatywny') NOT NULL,
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
