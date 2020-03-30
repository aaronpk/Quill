ALTER TABLE users
DROP COLUMN flightaware_username,
DROP COLUMN flightaware_apikey;

ALTER TABLE users
ADD COLUMN `channels` TEXT AFTER syndication_targets;
