-- BloodLink Database Schema
-- Database: bloodlink_db

CREATE DATABASE IF NOT EXISTS bloodlink_db;
USE bloodlink_db;

-- 1. Users Table
-- Stores information for Admins, Donors, and Finders
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL, -- Should be hashed in a real application
    role ENUM('admin', 'donor', 'finder') NOT NULL,
    hospital VARCHAR(150), -- Only for admins and donors
    status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
    
    -- Donor specific health information
    smoking ENUM('yes', 'no'),
    chronic_illness ENUM('yes', 'no'),
    illness_details TEXT,
    tattoo ENUM('yes', 'no'),
    travel ENUM('yes', 'no'),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Blood Requests Table
-- Stores requests submitted by finders
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group VARCHAR(5) NOT NULL,
    admitted_hospital VARCHAR(150) NOT NULL,
    patient_location TEXT NOT NULL,
    contacts TEXT NOT NULL, -- Can store multiple numbers as a string
    status ENUM('pending', 'fulfilled', 'cancelled') DEFAULT 'pending',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Sample Data for Testing
-- Default Admin
INSERT INTO users (fullname, email, phone, password, role, hospital, status) 
VALUES ('admin', 'admin@bloodlink.com', '01711223344', 'admin123', 'admin', 'City General Hospital', 'approved');

-- Sample Donor (Pending)
INSERT INTO users (fullname, email, phone, password, role, hospital, status, smoking, chronic_illness, tattoo, travel) 
VALUES ('John Doe', 'john@example.com', '01855667788', 'donor123', 'donor', 'Central Blood Bank', 'pending', 'no', 'no', 'no', 'no');

-- Sample Finder (Approved)
INSERT INTO users (fullname, email, phone, password, role, status) 
VALUES ('Sarah Smith', 'sarah@example.com', '01900998877', 'finder123', 'finder', 'approved');

-- Sample Blood Request
INSERT INTO blood_requests (blood_group, admitted_hospital, patient_location, contacts) 
VALUES ('B+', 'Dhaka Medical College Hospital', 'Ward 5, Bed 12', '01700000000, 01800000000');
