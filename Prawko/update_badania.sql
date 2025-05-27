-- Dodaj kolumny typ i status do tabeli badania
ALTER TABLE badania
ADD COLUMN typ ENUM('Podstawowe', 'Rozszerzone', 'Psychologiczne') NOT NULL DEFAULT 'Podstawowe',
ADD COLUMN status ENUM('Oczekujący', 'Zatwierdzony', 'Odrzucony') DEFAULT 'Oczekujący';

-- Zaktualizuj istniejące rekordy
UPDATE badania SET typ = 'Podstawowe', status = 'Zatwierdzony'; 