CREATE DATABASE IF NOT EXISTS practical14;
USE practical14;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;


INSERT INTO users (name, email, password, role) VALUES
('Demo Admin', 'admin@example.com', '$2y$10$6UUDQtwAC7CEm/fRMHbkaeLuOTDgSoYxTpRS35piMgpmI6597/oja', 'admin'),
('Demo Student', 'student@example.com', '$2y$10$6UUDQtwAC7CEm/fRMHbkaeLuOTDgSoYxTpRS35piMgpmI6597/oja', 'student');

INSERT INTO students (name) VALUES ('Aarav Patel'), ('Diya Shah'), ('Vivaan Mehta'), ('Ananya Desai'), ('Aditya Joshi');
INSERT INTO events (title, status) VALUES ('Web Workshop', 'Upcoming'), ('Coding Competition', 'Ongoing'), ('Sports Day', 'Completed');
INSERT INTO registrations (student_id, event_id, registered_at) VALUES
(1, 1, '2026-10-18 10:00:00'), (2, 1, '2026-10-18 11:00:00'),
(3, 2, '2026-10-19 09:00:00'), (4, 2, '2026-10-19 10:00:00'),
(5, 3, '2026-10-20 09:00:00'), (1, 2, '2026-10-20 10:00:00');
