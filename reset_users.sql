-- SQL Script to delete all users and reset the database
-- WARNING: This will delete ALL users and their associated data (passwords, API keys, notes)

-- Delete all users (this will cascade delete all related data)
DELETE FROM users;

-- Reset auto-increment counter to start from 1 again
ALTER TABLE users AUTO_INCREMENT = 1;

-- Verify deletion
SELECT COUNT(*) as total_users FROM users;
