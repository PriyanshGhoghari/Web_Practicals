CREATE DATABASE IF NOT EXISTS practical11;
USE practical11;

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
('Isha Trivedi', 'isha@example.com', 'BBA'),
('Arjun Dave', 'arjun@example.com', 'BCom'),
('Kavya Modi', 'kavya@example.com', 'BSc IT'),
('Krish Pandya', 'krish@example.com', 'BCA'),
('Riya Vyas', 'riya@example.com', 'BBA'),
('Dhruv Gandhi', 'dhruv@example.com', 'BCom'),
('Meera Bhatt', 'meera@example.com', 'BSc IT'),
('Dev Thakkar', 'dev@example.com', 'BCA'),
('Nisha Rana', 'nisha@example.com', 'BBA'),
('Om Parmar', 'om@example.com', 'BCom'),
('Pooja Solanki', 'pooja@example.com', 'BSc IT'),
('Yash Chauhan', 'yash@example.com', 'BCA'),
('Sneha Prajapati', 'sneha@example.com', 'BBA'),
('Harsh Shah', 'harsh@example.com', 'BCom'),
('Tanya Patel', 'tanya@example.com', 'BSc IT');
