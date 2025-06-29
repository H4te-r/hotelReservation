<?php
require_once 'config/database.php';

echo "<h2>Testing Database Connection</h2>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test query for hotel
    $stmt = $pdo->query("SELECT COUNT(*) as hotel_count FROM hotel");
    $result = $stmt->fetch();
    echo "<p>🏨 Found {$result['hotel_count']} hotel in database</p>";
    
    // Test query for rooms
    $stmt = $pdo->query("SELECT COUNT(*) as room_count FROM rooms");
    $result = $stmt->fetch();
    echo "<p>🛏️ Found {$result['room_count']} rooms in database</p>";
    
    // Test query for reservations
    $stmt = $pdo->query("SELECT COUNT(*) as reservation_count FROM reservations");
    $result = $stmt->fetch();
    echo "<p>📅 Found {$result['reservation_count']} reservations in database</p>";
    
    // Show hotel info
    $stmt = $pdo->query("SELECT name, address FROM hotel LIMIT 1");
    $hotel = $stmt->fetch();
    if ($hotel) {
        echo "<p><strong>Hotel:</strong> {$hotel['name']}</p>";
        echo "<p><strong>Address:</strong> {$hotel['address']}</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Please make sure:</strong></p>";
    echo "<ul>";
    echo "<li>The database 'hotel_reservation_system' exists</li>";
    echo "<li>You've imported the database.sql file</li>";
    echo "<li>Database credentials in config/database.php are correct</li>";
    echo "</ul>";
}
?> 