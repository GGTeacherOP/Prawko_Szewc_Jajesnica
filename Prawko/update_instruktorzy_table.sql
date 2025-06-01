-- Drop existing table if exists
DROP TABLE IF EXISTS `instruktorzy`;

-- Create instruktorzy table
CREATE TABLE `instruktorzy` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `imie` VARCHAR(50) NOT NULL,
    `nazwisko` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `telefon` VARCHAR(20) NOT NULL,
    `kategorie` VARCHAR(50) NOT NULL,
    `status` ENUM('Aktywny', 'Nieaktywny') DEFAULT 'Aktywny',
    `data_dodania` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add some sample instructors
INSERT INTO `instruktorzy` (`imie`, `nazwisko`, `email`, `telefon`, `kategorie`) VALUES
('Jan', 'Kowalski', 'jan.kowalski@example.com', '123456789', 'B'),
('Anna', 'Nowak', 'anna.nowak@example.com', '987654321', 'A,B'),
('Piotr', 'Wiśniewski', 'piotr.wisniewski@example.com', '456789123', 'B,C');

-- Add indexes for better performance
CREATE INDEX `idx_email` ON `instruktorzy` (`email`);
CREATE INDEX `idx_status` ON `instruktorzy` (`status`); 