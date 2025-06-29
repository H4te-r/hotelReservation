-- Safe Database Fix Script
-- Run this in phpMyAdmin to fix the email column issue safely

USE hotel_reservation_system;

-- Step 1: Add email column (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'email') = 0,
    'ALTER TABLE reservations ADD COLUMN email VARCHAR(255) NOT NULL AFTER guest_name',
    'SELECT "Email column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Copy data from guest_email to email (if guest_email exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'guest_email') > 0,
    'UPDATE reservations SET email = guest_email WHERE guest_email IS NOT NULL AND email = ""',
    'SELECT "No guest_email column to copy from" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add phone column (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'phone') = 0,
    'ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email',
    'SELECT "Phone column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Copy data from guest_phone to phone (if guest_phone exists)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'guest_phone') > 0,
    'UPDATE reservations SET phone = guest_phone WHERE guest_phone IS NOT NULL AND phone = ""',
    'SELECT "No guest_phone column to copy from" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Add booking_id column (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'booking_id') = 0,
    'ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id',
    'SELECT "Booking_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Add num_guests column (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'num_guests') = 0,
    'ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date',
    'SELECT "Num_guests column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Generate booking IDs for existing reservations (only if booking_id is empty)
UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) 
WHERE booking_id IS NULL OR booking_id = '';

-- Step 8: Make booking_id NOT NULL (only if it's not already)
SET @sql = (SELECT IF(
    (SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'booking_id') = 'YES',
    'ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL',
    'SELECT "Booking_id already NOT NULL" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 9: Add unique constraint to booking_id (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
     AND TABLE_NAME = 'reservations' 
     AND COLUMN_NAME = 'booking_id' 
     AND CONSTRAINT_NAME = 'unique_booking_id') = 0,
    'ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id)',
    'SELECT "Unique constraint already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show final table structure
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'hotel_reservation_system' 
AND TABLE_NAME = 'reservations' 
ORDER BY ORDINAL_POSITION; 