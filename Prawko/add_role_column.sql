ALTER TABLE uzytkownicy
ADD COLUMN rola ENUM('kursant', 'instruktor') DEFAULT 'kursant' NOT NULL; 