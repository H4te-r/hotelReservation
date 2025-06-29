-- Single Hotel Room Reservation System Database
-- Create database
CREATE DATABASE IF NOT EXISTS hotel_reservation_system;
USE hotel_reservation_system;

-- Drop existing tables if they exist (for fresh start)
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS hotel;

-- Single hotel table
CREATE TABLE hotel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    description TEXT,
    rating DECIMAL(2,1) DEFAULT 0.0,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    room_number VARCHAR(50) NOT NULL UNIQUE,
    room_type VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    size INT,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    has_wifi BOOLEAN DEFAULT TRUE,
    has_tv BOOLEAN DEFAULT TRUE,
    has_ac BOOLEAN DEFAULT TRUE,
    is_available BOOLEAN DEFAULT TRUE,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Reservations table
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE NOT NULL,
    room_id INT NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    num_guests INT NOT NULL,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert single hotel data
INSERT INTO hotel (name, address, phone, email, description, rating, image_url) VALUES
('Grand Plaza Hotel', '123 Main Street, Downtown, City', '+1-555-0123', 'info@grandplaza.com', 'Luxury hotel in the heart of downtown with stunning city views and world-class amenities.', 4.5, 'hotel1.jpg');

-- Insert sample rooms
INSERT INTO rooms (name, room_number, room_type, capacity, size, price_per_night, description, has_wifi, has_tv, has_ac, image_url) VALUES
('Standard Room 101', '101', 'Standard Room', 2, 300, 5000.00, 'Comfortable standard room with city view, queen bed, and modern amenities', TRUE, TRUE, TRUE, 'room1.jpg'),
('Standard Room 102', '102', 'Standard Room', 2, 300, 5000.00, 'Cozy standard room with city view, queen bed, and modern amenities', TRUE, TRUE, TRUE, 'room2.jpg'),
('Deluxe Room 201', '201', 'Deluxe Room', 3, 450, 8500.00, 'Spacious deluxe room with premium amenities, king bed, and city skyline view', TRUE, TRUE, TRUE, 'room3.jpg'),
('Deluxe Room 202', '202', 'Deluxe Room', 3, 450, 8500.00, 'Elegant deluxe room with premium amenities, king bed, and city skyline view', TRUE, TRUE, TRUE, 'room4.jpg'),
('Suite 301', '301', 'Suite', 4, 600, 15000.00, 'Luxury suite with separate living area, king bed, and panoramic city views', TRUE, TRUE, TRUE, 'room5.jpg'),
('Suite 302', '302', 'Suite', 4, 600, 15000.00, 'Executive suite with separate living area, king bed, and panoramic city views', TRUE, TRUE, TRUE, 'room6.jpg'),
('Presidential Suite 401', '401', 'Presidential Suite', 6, 1000, 25000.00, 'Ultimate luxury with multiple rooms, private balcony, and premium services', TRUE, TRUE, TRUE, 'room7.jpg');

-- Insert admin users
INSERT INTO admin_users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotel.com', 'System Administrator', 'admin'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@hotel.com', 'Hotel Manager', 'manager');

-- Insert sample reservations
INSERT INTO reservations (booking_id, room_id, guest_name, email, phone, check_in_date, check_out_date, num_guests, total_price, status) VALUES
('BK20240115001', 1, 'John Doe', 'john@example.com', '+1-555-0001', '2024-01-15', '2024-01-17', 2, 10000.00, 'confirmed'),
('BK20240120002', 2, 'Jane Smith', 'jane@example.com', '+1-555-0002', '2024-01-20', '2024-01-22', 2, 10000.00, 'pending'),
('BK20240125003', 3, 'Mike Johnson', 'mike@example.com', '+1-555-0003', '2024-01-25', '2024-01-27', 3, 17000.00, 'confirmed'); 