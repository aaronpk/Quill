ALTER TABLE users
ADD COLUMN `micropub_slug_field` VARCHAR(255) NOT NULL DEFAULT 'mp-slug' AFTER `micropub_response`;

UPDATE users
SET micropub_slug_field = 'slug';
