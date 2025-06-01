-- Drop existing tables in correct order
DROP TABLE IF EXISTS `zapisy_badan`;
DROP TABLE IF EXISTS `badania`;

-- Create the badania table with correct structure
CREATE TABLE `badania` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `data` DATE NOT NULL,
    `godzina` TIME NOT NULL,
    `miejsce` VARCHAR(255) NOT NULL,
    `max_osob` INT NOT NULL DEFAULT 10,
    `status` ENUM('Aktywne', 'Zakończone', 'Anulowane') DEFAULT 'Aktywne'
);

-- Create zapisy_badan table
CREATE TABLE `zapisy_badan` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `badanie_id` INT NOT NULL,
    `uzytkownik_id` INT NOT NULL,
    `data_zapisu` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`badanie_id`) REFERENCES `badania`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownicy`(`id`) ON DELETE CASCADE
);

-- Add indexes for better performance
CREATE INDEX `idx_badanie_id` ON `zapisy_badan` (`badanie_id`);
CREATE INDEX `idx_uzytkownik_id` ON `zapisy_badan` (`uzytkownik_id`); 