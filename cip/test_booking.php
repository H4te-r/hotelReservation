<?php
require_once 'config/database.php';

echo "<h2>Booking Form Database Test</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Test 1: Check if we can get hotel info
    echo "<h3>Test 1: Hotel Information</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM hotel LIMIT 1");
        $hotel = $stmt->fetch();
        if ($hotel) {
            echo "<p>✅ Hotel found: " . htmlspecialchars($hotel['name']) . "</p>";
        } else {
            echo "<p>❌ No hotel found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Hotel query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 2: Check if we can get rooms
    echo "<h3>Test 2: Room Information</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM rooms WHERE is_available = 1 LIMIT 1");
        $room = $stmt->fetch();
        if ($room) {
            echo "<p>✅ Room found: " . htmlspecialchars($room['name'] ?? $room['room_type']) . "</p>";
        } else {
            echo "<p>❌ No available rooms found</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Room query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 3: Check if we can insert a test reservation
    echo "<h3>Test 3: Reservation Insert Test</h3>";
    try {
        // Get a room ID
        $stmt = $pdo->query("SELECT id FROM rooms WHERE is_available = 1 LIMIT 1");
        $room = $stmt->fetch();
        
        if ($room) {
            // Try to insert a test reservation
            $testBookingId = 'TEST' . date('YmdHis');
            $stmt = $pdo->prepare("
                INSERT INTO reservations (
                    booking_id, guest_name, email, phone, check_in_date, 
                    check_out_date, num_guests, room_id, special_requests, total_price, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $stmt->execute([
                $testBookingId,
                'Test Guest',
                'test@example.com',
                '123-456-7890',
                '2024-12-25',
                '2024-12-27',
                2,
                $room['id'],
                'Test reservation',
                300.00
            ]);
            
            echo "<p>✅ Test reservation inserted successfully!</p>";
            
            // Clean up test data
            $pdo->exec("DELETE FROM reservations WHERE booking_id = '$testBookingId'");
            echo "<p>✅ Test data cleaned up</p>";
            
        } else {
            echo "<p>❌ No rooms available for testing</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Reservation insert failed: " . $e->getMessage() . "</p>";
        
        // Show the exact error details
        echo "<h4>Error Details:</h4>";
        echo "<p>Error Code: " . $e->getCode() . "</p>";
        echo "<p>Error Message: " . $e->getMessage() . "</p>";
        
        // Check table structure
        echo "<h4>Current Reservations Table Structure:</h4>";
        $stmt = $pdo->query("DESCRIBE reservations");
        $columns = $stmt->fetchAll();
        
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>" . htmlspecialchars($column['Field']) . " (" . htmlspecialchars($column['Type']) . ")</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>✅ Test Complete!</h3>";
    echo "<p><a href='bookingform.php'>Try Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?> 