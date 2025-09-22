## Database Setup

1. Open **phpMyAdmin** or MySQL console.
2. Run the following SQL script to create the database and tables:


'''sql
-- Drop existing database if you want a fresh start
DROP DATABASE IF EXISTS faculty_worklog;

-- Create the database
CREATE DATABASE faculty_worklog;
USE faculty_worklog;

-- Drop tables if they exist
DROP TABLE IF EXISTS worklogs;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('faculty', 'admin') NOT NULL,
    department VARCHAR(100) DEFAULT ''  -- added department column
);

-- Insert sample users
INSERT INTO users (name, email, password, role, department) VALUES
('Faculty One', 'faculty1@example.com', '1234', 'faculty', 'Mathematics'),
('Faculty Two', 'faculty2@example.com', '1234', 'faculty', 'Physics'),
('Admin', 'admin@example.com', '1234', 'admin', '');

-- Create worklogs table
CREATE TABLE worklogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    date DATE NOT NULL,
    time_from TIME NOT NULL,
    time_to TIME NOT NULL,
    domain VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    remarks TEXT DEFAULT '',
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample worklogs
INSERT INTO worklogs (faculty_id, date, time_from, time_to, domain, description, status)
VALUES
(1, '2025-09-22', '09:00:00', '11:00:00', 'Mathematics', 'Prepared lecture notes on Linear Algebra', 'Pending'),
(2, '2025-09-22', '10:00:00', '12:00:00', 'Physics', 'Conducted lab experiments on Mechanics', 'Pending');
