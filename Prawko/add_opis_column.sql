-- Add opis column to platnosci table
ALTER TABLE platnosci
ADD COLUMN IF NOT EXISTS opis VARCHAR(255) DEFAULT NULL; 