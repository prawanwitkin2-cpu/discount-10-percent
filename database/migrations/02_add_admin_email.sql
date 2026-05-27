ALTER TABLE admins ADD COLUMN email VARCHAR(120) NULL AFTER username;
UPDATE admins SET email = 'panupun.pun@gmail.com' WHERE id = 1;
