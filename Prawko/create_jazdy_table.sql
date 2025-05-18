-- Drop tables if they exist
DROP TABLE IF EXISTS jazdy;
DROP TABLE IF EXISTS instruktorzy;
DROP TABLE IF EXISTS pojazdy;

-- Create instructors table
CREATE TABLE instruktorzy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    kategorie_uprawnien SET('A','B','C','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create vehicles table
CREATE TABLE pojazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    numer_rejestracyjny VARCHAR(20) NOT NULL,
    kategoria_prawa_jazdy ENUM('A','B','C','D') NOT NULL,
    stan_techniczny ENUM('Dobry','Średni','Do Naprawy') DEFAULT 'Dobry'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create driving lessons table
CREATE TABLE jazdy (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uzytkownik_id INT(11) NOT NULL,
    instruktor_id INT(11) NOT NULL,
    pojazd_id INT(11) NOT NULL,
    data_jazdy DATE NOT NULL,
    godzina_rozpoczecia TIME NOT NULL,
    godzina_zakonczenia TIME NOT NULL,
    status ENUM('Zaplanowana','Zakończona','Anulowana') DEFAULT 'Zaplanowana',
    FOREIGN KEY (instruktor_id) REFERENCES instruktorzy(id),
    FOREIGN KEY (pojazd_id) REFERENCES pojazdy(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO instruktorzy (imie, nazwisko, email, telefon, kategorie_uprawnien) VALUES
('Jan', 'Kowalski', 'jan.kowalski@example.com', '123456789', 'A,B'),
('Anna', 'Nowak', 'anna.nowak@example.com', '987654321', 'B,C'),
('Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', '555666777', 'C,D');

INSERT INTO pojazdy (marka, model, numer_rejestracyjny, kategoria_prawa_jazdy) VALUES
('Honda', 'CBR500', 'SBI12345', 'A'),
('Toyota', 'Yaris', 'SBI54321', 'B'),
('Scania', 'R450', 'SBI98765', 'C'),
('Mercedes', 'Tourismo', 'SBI45678', 'D'); 