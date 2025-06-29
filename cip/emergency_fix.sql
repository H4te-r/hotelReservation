-- Emergency Database Fix
-- Run this in phpMyAdmin to fix the email column issue

USE hotel_reservation_system;

-- Step 1: Add email column (if it doesn't exist)
ALTER TABLE reservations ADD COLUMN email VARCHAR(255) NOT NULL AFTER guest_name;

-- Step 2: Copy data from guest_email to email (if guest_email exists)
UPDATE reservations SET email = guest_email WHERE guest_email IS NOT NULL;

-- Step 3: Add phone column (if it doesn't exist)
ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email;

-- Step 4: Copy data from guest_phone to phone (if guest_phone exists)
UPDATE reservations SET phone = guest_phone WHERE guest_phone IS NOT NULL;

-- Step 5: Add booking_id column (if it doesn't exist)
ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id;

-- Step 6: Add num_guests column (if it doesn't exist)
ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;

-- Step 7: Generate booking IDs for existing reservations
UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL OR booking_id = '';

-- Step 8: Make booking_id NOT NULL
ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL;

-- Step 9: Add unique constraint to booking_id
ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id);

-- Step 10: Remove old columns (optional - uncomment if you want to clean up)
-- ALTER TABLE reservations DROP COLUMN guest_email;
-- ALTER TABLE reservations DROP COLUMN guest_phone; 