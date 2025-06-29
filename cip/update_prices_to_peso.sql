-- Update room prices to Philippine Peso (Realistic Luxury Hotel Rates)
-- Standard Rooms: ₱5,000 per night (lowest price)
UPDATE rooms SET price_per_night = 5000.00 WHERE room_type = 'Standard Room';

-- Deluxe Rooms: ₱8,500 per night  
UPDATE rooms SET price_per_night = 8500.00 WHERE room_type = 'Deluxe Room';

-- Suites: ₱15,000 per night
UPDATE rooms SET price_per_night = 15000.00 WHERE room_type = 'Suite';

-- Presidential Suite: ₱25,000 per night
UPDATE rooms SET price_per_night = 25000.00 WHERE room_type = 'Presidential Suite';

-- Update existing reservation total prices to reflect new room prices
-- This will recalculate based on the number of nights
UPDATE reservations r 
JOIN rooms rm ON r.room_id = rm.id 
SET r.total_price = rm.price_per_night * DATEDIFF(r.check_out_date, r.check_in_date); 