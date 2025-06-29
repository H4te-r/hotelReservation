-- Single Hotel Room Reservation System Database
-- Create database
CREATE DATABASE IF NOT EXISTS hotel_reservation_system;
USE hotel_reservation_system;

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
    room_number VARCHAR(50) NOT NULL UNIQUE,
    room_type VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    is_available BOOLEAN DEFAULT TRUE,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Reservations table
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    guest_email VARCHAR(255) NOT NULL,
    guest_phone VARCHAR(20),
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
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
INSERT INTO rooms (room_number, room_type, capacity, price_per_night, description, image_url) VALUES
('101', 'Standard Room', 2, 150.00, 'Comfortable standard room with city view, queen bed, and modern amenities', 'room1.jpg'),
('102', 'Standard Room', 2, 150.00, 'Cozy standard room with city view, queen bed, and modern amenities', 'room2.jpg'),
('201', 'Deluxe Room', 3, 250.00, 'Spacious deluxe room with premium amenities, king bed, and city skyline view', 'room3.jpg'),
('202', 'Deluxe Room', 3, 250.00, 'Elegant deluxe room with premium amenities, king bed, and city skyline view', 'room4.jpg'),
('301', 'Suite', 4, 400.00, 'Luxury suite with separate living area, king bed, and panoramic city views', 'room5.jpg'),
('302', 'Suite', 4, 400.00, 'Executive suite with separate living area, king bed, and panoramic city views', 'room6.jpg'),
('401', 'Presidential Suite', 6, 800.00, 'Ultimate luxury with multiple rooms, private balcony, and premium services', 'room7.jpg');

-- Insert admin users
INSERT INTO admin_users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotel.com', 'System Administrator', 'admin'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@hotel.com', 'Hotel Manager', 'manager');

-- Insert sample reservations
INSERT INTO reservations (room_id, guest_name, guest_email, guest_phone, check_in_date, check_out_date, total_price, status) VALUES
(1, 'John Doe', 'john@example.com', '+1-555-0001', '2024-01-15', '2024-01-17', 300.00, 'confirmed'),
(2, 'Jane Smith', 'jane@example.com', '+1-555-0002', '2024-01-20', '2024-01-22', 300.00, 'pending'),
(3, 'Mike Johnson', 'mike@example.com', '+1-555-0003', '2024-01-25', '2024-01-27', 500.00, 'confirmed'); 