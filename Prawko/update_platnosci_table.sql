-- Add badanie_id column to platnosci table
ALTER TABLE platnosci
ADD COLUMN badanie_id INT(11) NULL,
ADD FOREIGN KEY (badanie_id) REFERENCES badania(id);

-- Update existing records to set badanie_id to NULL where it's not set
UPDATE platnosci SET badanie_id = NULL WHERE badanie_id IS NULL; 