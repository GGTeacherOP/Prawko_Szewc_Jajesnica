-- Add password and role columns to instruktorzy table
ALTER TABLE instruktorzy 
ADD COLUMN haslo VARCHAR(255) NOT NULL AFTER telefon,
ADD COLUMN rola ENUM('instruktor') DEFAULT 'instruktor';

-- Update existing instructors with default password (hasło123)
UPDATE instruktorzy 
SET haslo = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE haslo = '';

-- Insert test instructor account
INSERT INTO instruktorzy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien, rola)
VALUES ('Jan', 'Kowalski', 'jan.kowalski@example.com', '123456789', 
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'A,B,C', 'instruktor')
ON DUPLICATE KEY UPDATE 
    haslo = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    rola = 'instruktor'; 