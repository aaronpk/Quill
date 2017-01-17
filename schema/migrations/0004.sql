ALTER TABLE users
ADD COLUMN `micropub_syndicate_field` VARCHAR(255) NOT NULL DEFAULT 'mp-syndicate-to' AFTER `micropub_slug_field`;

UPDATE users
SET micropub_syndicate_field = 'syndicate-to';
