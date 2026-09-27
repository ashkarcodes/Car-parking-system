-- =========================================================
-- Online Car Parking System - Database Schema
-- =========================================================

CREATE DATABASE IF NOT EXISTS parking_db;
USE parking_db;

-- ---------------------------------------------------------
-- ADMINS
-- ---------------------------------------------------------
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin -> email: admin@parking.com | password: admin123
INSERT INTO admins (name, email, password) VALUES
('Super Admin', 'admin@parking.com', '$2y$10$LqzKX0bZ2s4nJ8mYpFo0GO2n6zR8oDcW9YqU2nH1WQeS3fZ0eQeCa');

-- ---------------------------------------------------------
-- USERS
-- ---------------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    status ENUM('active','blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- PARKING SPOTS
-- ---------------------------------------------------------
CREATE TABLE parking_spots (
    spot_id INT AUTO_INCREMENT PRIMARY KEY,
    spot_number VARCHAR(20) NOT NULL UNIQUE,
    location VARCHAR(150) NOT NULL,
    floor_level VARCHAR(20) DEFAULT 'Ground',
    vehicle_type ENUM('car','bike','both') DEFAULT 'car',
    rate_per_hour DECIMAL(6,2) DEFAULT 20.00,
    status ENUM('available','booked','occupied') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- BOOKINGS
-- Lifecycle: booked -> parked -> exited (awaiting admin release) -> completed
-- ---------------------------------------------------------
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    spot_id INT NOT NULL,
    user_id INT NOT NULL,
    vehicle_number VARCHAR(30) NOT NULL,
    vehicle_type ENUM('car','bike') DEFAULT 'car',
    status ENUM('booked','parked','exited','completed','cancelled') DEFAULT 'booked',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parked_at TIMESTAMP NULL,
    exited_at TIMESTAMP NULL,
    released_at TIMESTAMP NULL,
    FOREIGN KEY (spot_id) REFERENCES parking_spots(spot_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Sample parking spots to get started
INSERT INTO parking_spots (spot_number, location, floor_level, vehicle_type, rate_per_hour) VALUES
('A-01', 'Main Entrance Block', 'Ground', 'car', 20.00),
('A-02', 'Main Entrance Block', 'Ground', 'car', 20.00),
('A-03', 'Main Entrance Block', 'Ground', 'both', 15.00),
('B-01', 'North Wing', 'First Floor', 'car', 25.00),
('B-02', 'North Wing', 'First Floor', 'bike', 10.00),
('C-01', 'South Wing', 'Second Floor', 'car', 25.00);
