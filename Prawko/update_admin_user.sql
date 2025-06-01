-- Add admin user if not exists
INSERT INTO uzytkownicy (email, haslo, imie, nazwisko, rola, telefon, kategoria_prawa_jazdy)
SELECT 'admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', 'admin', '000000000', 'B'
WHERE NOT EXISTS (
    SELECT 1 FROM uzytkownicy WHERE email = 'admin1'
);

-- Update admin user if exists
UPDATE uzytkownicy 
SET haslo = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    rola = 'admin'
WHERE email = 'admin1'; 