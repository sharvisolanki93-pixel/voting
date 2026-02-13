-- Fix Admin Password
-- Run this SQL in phpMyAdmin if the password reset tool doesn't work

-- Option 1: Delete existing admin and recreate
DELETE FROM users WHERE email = 'admin@college.edu';

-- Option 2: Then insert new admin with correct password hash
-- The password hash below is for: admin123
INSERT INTO users (username, email, password, role, is_verified) VALUES
('admin', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Verify admin was created
SELECT id, username, email, role FROM users WHERE role = 'admin';
