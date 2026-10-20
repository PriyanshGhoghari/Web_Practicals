CREATE DATABASE IF NOT EXISTS practical13;
USE practical13;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(30) NOT NULL
);


INSERT IGNORE INTO students (name, email, course) VALUES
('Aarav Patel', 'aarav@example.com', 'BCA'),
('Diya Shah', 'diya@example.com', 'BBA'),
('Vivaan Mehta', 'vivaan@example.com', 'BCom'),
('Ananya Desai', 'ananya@example.com', 'BSc IT'),
('Aditya Joshi', 'aditya@example.com', 'BCA'),
('Isha Trivedi', 'isha@example.com', 'BBA');
