

ALTER TABLE users
ADD COLUMN `flightaware_username` VARCHAR(255) NOT NULL DEFAULT '',
ADD COLUMN `flightaware_apikey` VARCHAR(255) NOT NULL DEFAULT '';
