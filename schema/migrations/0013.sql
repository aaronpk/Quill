ALTER TABLE users
ADD COLUMN `micropub_token_expiration` datetime DEFAULT NULL;
