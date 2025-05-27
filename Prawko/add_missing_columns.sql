-- Add missing columns to uzytkownicy table
ALTER TABLE uzytkownicy
ADD COLUMN IF NOT EXISTS login VARCHAR(50) UNIQUE AFTER id,
ADD COLUMN IF NOT EXISTS data_urodzenia DATE AFTER telefon;

-- Update login values for existing users
UPDATE uzytkownicy SET login = CONCAT(LOWER(imie), '_', LOWER(nazwisko), '_', id) WHERE login IS NULL;

-- Set a default date for existing users (you may want to update this later)
UPDATE uzytkownicy SET data_urodzenia = CURRENT_DATE WHERE data_urodzenia IS NULL; 