-- Simple Database Fix Script
-- Run these commands one by one in phpMyAdmin

USE hotel_reservation_system;

-- Step 1: Add booking_id column (nullable first)
ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id;

-- Step 2: Add num_guests column
ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;

-- Step 3: Rename email column
ALTER TABLE reservations CHANGE COLUMN guest_email email VARCHAR(255) NOT NULL;

-- Step 4: Rename phone column
ALTER TABLE reservations CHANGE COLUMN guest_phone phone VARCHAR(20);

-- Step 5: Generate unique booking IDs for existing reservations
UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL;

-- Step 6: Make booking_id NOT NULL and add unique constraint
ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL;
ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id);

-- Step 7: Add missing columns to rooms table
ALTER TABLE rooms ADD COLUMN name VARCHAR(100) NOT NULL DEFAULT 'Room' AFTER id;
ALTER TABLE rooms ADD COLUMN size INT DEFAULT 300 AFTER capacity;
ALTER TABLE rooms ADD COLUMN has_wifi BOOLEAN DEFAULT TRUE AFTER description;
ALTER TABLE rooms ADD COLUMN has_tv BOOLEAN DEFAULT TRUE AFTER has_wifi;
ALTER TABLE rooms ADD COLUMN has_ac BOOLEAN DEFAULT TRUE AFTER has_tv;

-- Step 8: Update room names and features
UPDATE rooms SET name = CONCAT(room_type, ' ', room_number);
UPDATE rooms SET size = 300 WHERE room_type = 'Standard Room';
UPDATE rooms SET size = 450 WHERE room_type = 'Deluxe Room';
UPDATE rooms SET size = 600 WHERE room_type = 'Suite';
UPDATE rooms SET size = 1000 WHERE room_type = 'Presidential Suite';
UPDATE rooms SET has_wifi = TRUE, has_tv = TRUE, has_ac = TRUE;

-- Step 9: Calculate total prices for existing reservations
UPDATE reservations r 
JOIN rooms rm ON r.room_id = rm.id 
SET r.total_price = rm.price_per_night * DATEDIFF(r.check_out_date, r.check_in_date)
WHERE r.total_price IS NULL; 