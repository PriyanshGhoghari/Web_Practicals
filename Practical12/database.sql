CREATE DATABASE IF NOT EXISTS practical12 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE practical12;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    status VARCHAR(20) NOT NULL,
    poster VARCHAR(100) NOT NULL DEFAULT ''
);


INSERT INTO events (title, event_date, venue, status) VALUES
('Web Development Workshop', '2026-11-10', 'Computer Lab', 'Upcoming'),
('College Sports Day', '2026-11-15', 'College Ground', 'Upcoming'),
('Coding Competition', '2026-10-20', 'Lab 2', 'Ongoing'),
('Welcome Ceremony', '2026-09-01', 'Auditorium', 'Completed'),
('Photography Walk', '2026-10-25', 'Main Gate', 'Cancelled');
