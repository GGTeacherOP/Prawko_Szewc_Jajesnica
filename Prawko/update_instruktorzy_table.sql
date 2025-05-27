-- Drop existing table
DROP TABLE IF EXISTS instruktorzy;

-- Create new table with uzytkownik_id as primary key
CREATE TABLE instruktorzy (
    uzytkownik_id INT(11) PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    kategoria VARCHAR(100) NOT NULL,
    FOREIGN KEY (uzytkownik_id) REFERENCES uzytkownicy(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 