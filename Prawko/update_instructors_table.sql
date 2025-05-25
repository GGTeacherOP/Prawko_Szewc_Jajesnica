-- Zmień nazwę tabeli instruktorzy na pracownicy
RENAME TABLE instruktorzy TO pracownicy;

-- Dodaj kolumnę rola jeśli nie istnieje
ALTER TABLE pracownicy 
    ADD COLUMN rola ENUM('instruktor', 'ksiegowy', 'admin') NOT NULL DEFAULT 'instruktor';

-- Przykładowi pracownicy
INSERT INTO pracownicy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien, rola)
VALUES ('Anna', 'Księgowa', 'ksiegowa@firma.pl', '123456789', 'haslo123', '', 'ksiegowy'),
       ('Adam', 'Admin', 'admin@firma.pl', '987654321', 'admin123', '', 'admin'); 