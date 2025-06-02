-- Add owner role to pracownicy table
ALTER TABLE pracownicy MODIFY COLUMN rola ENUM('instruktor', 'ksiegowy', 'admin', 'wlasciciel') NOT NULL DEFAULT 'instruktor';

-- Insert owner account
INSERT INTO pracownicy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien, rola) 
VALUES ('Właściciel', 'Szkoły', 'wlasciciel@szkola.pl', '999888777', '123', '', 'wlasciciel'); 