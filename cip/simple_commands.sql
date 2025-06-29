-- Simple Individual Commands
-- Run these one by one in phpMyAdmin

USE hotel_reservation_system;

-- Command 1: Add email column (run this first)
ALTER TABLE reservations ADD COLUMN email VARCHAR(255) NOT NULL AFTER guest_name;

-- Command 2: Add phone column (run this second)
ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email;

-- Command 3: Add num_guests column (run this third)
ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;

-- Command 4: Generate booking IDs (run this fourth)
UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL OR booking_id = '';

-- Command 5: Make booking_id NOT NULL (run this fifth)
ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL;

-- Command 6: Add unique constraint (run this last)
ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id); 