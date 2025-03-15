-- Drop the database if it exists
DROP DATABASE IF EXISTS queue_system;

-- Create the database
CREATE DATABASE queue_system;
USE queue_system;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin'
);

-- Insert an initial admin user
-- The password below is generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$wH9Lq0x60ogc4.F0NLb59uPCvR5ZClA7v3HkUus3.7c8EReF9Zyhy', 'admin');

-- Create the queue table
CREATE TABLE queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number INT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'waiting'
);
