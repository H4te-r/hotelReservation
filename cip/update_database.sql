-- Database Migration Script
-- Run this to update your existing database with missing columns

USE hotel_reservation_system;

-- Add missing columns to reservations table (without defaults to avoid unique constraint issues)
ALTER TABLE reservations 
ADD COLUMN booking_id VARCHAR(50) AFTER id,
ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;

-- Rename existing columns to match the booking form
ALTER TABLE reservations 
CHANGE COLUMN guest_email email VARCHAR(255) NOT NULL,
CHANGE COLUMN guest_phone phone VARCHAR(20);

-- Make total_price nullable (will be calculated)
ALTER TABLE reservations 
MODIFY COLUMN total_price DECIMAL(10,2) NULL;

-- Add missing columns to rooms table
ALTER TABLE rooms 
ADD COLUMN name VARCHAR(100) NOT NULL DEFAULT 'Room' AFTER id,
ADD COLUMN size INT DEFAULT 300 AFTER capacity,
ADD COLUMN has_wifi BOOLEAN DEFAULT TRUE AFTER description,
ADD COLUMN has_tv BOOLEAN DEFAULT TRUE AFTER has_wifi,
ADD COLUMN has_ac BOOLEAN DEFAULT TRUE AFTER has_tv;

-- Update existing rooms with proper names and features
UPDATE rooms SET 
name = CONCAT(room_type, ' ', room_number),
size = CASE 
    WHEN room_type = 'Standard Room' THEN 300
    WHEN room_type = 'Deluxe Room' THEN 450
    WHEN room_type = 'Suite' THEN 600
    WHEN room_type = 'Presidential Suite' THEN 1000
    ELSE 300
END,
has_wifi = TRUE,
has_tv = TRUE,
has_ac = TRUE;

-- Generate unique booking IDs for existing reservations
UPDATE reservations SET 
booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0'))
WHERE booking_id IS NULL;

-- Now make booking_id NOT NULL and UNIQUE
ALTER TABLE reservations 
MODIFY COLUMN booking_id VARCHAR(50) NOT NULL,
ADD UNIQUE KEY unique_booking_id (booking_id);

-- Update existing reservations with num_guests (default to 2)
UPDATE reservations SET num_guests = 2 WHERE num_guests = 1;

-- Calculate total_price for existing reservations
UPDATE reservations r 
JOIN rooms rm ON r.room_id = rm.id 
SET r.total_price = rm.price_per_night * DATEDIFF(r.check_out_date, r.check_in_date)
WHERE r.total_price IS NULL; 