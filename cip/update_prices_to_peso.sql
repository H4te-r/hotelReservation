-- Update room prices to Philippine Peso
-- Standard Rooms: ₱8,500 per night
UPDATE rooms SET price_per_night = 8500.00 WHERE room_type = 'Standard Room';

-- Deluxe Rooms: ₱12,500 per night  
UPDATE rooms SET price_per_night = 12500.00 WHERE room_type = 'Deluxe Room';

-- Suites: ₱18,500 per night
UPDATE rooms SET price_per_night = 18500.00 WHERE room_type = 'Suite';

-- Presidential Suite: ₱35,000 per night
UPDATE rooms SET price_per_night = 35000.00 WHERE room_type = 'Presidential Suite';

-- Update existing reservation total prices to reflect new room prices
-- This will recalculate based on the number of nights
UPDATE reservations r 
JOIN rooms rm ON r.room_id = rm.id 
SET r.total_price = rm.price_per_night * DATEDIFF(r.check_out_date, r.check_in_date); 