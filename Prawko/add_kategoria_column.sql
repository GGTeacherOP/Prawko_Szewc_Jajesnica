-- Add kategoria_prawa_jazdy column if it doesn't exist
ALTER TABLE uzytkownicy
ADD COLUMN IF NOT EXISTS kategoria_prawa_jazdy ENUM('A', 'B', 'C', 'D') DEFAULT NULL; 