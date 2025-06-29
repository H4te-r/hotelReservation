<?php
require_once 'config/database.php';

echo "<h2>Database Column Check</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Get current table structure
    $stmt = $pdo->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll();
    
    echo "<h3>Current Reservations Table Columns:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $columnNames = [];
    foreach ($columns as $column) {
        $columnNames[] = $column['Field'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Required Columns for Booking Form:</h3>";
    
    $requiredColumns = [
        'id' => 'Primary key',
        'booking_id' => 'Unique booking identifier',
        'guest_name' => 'Guest full name',
        'email' => 'Guest email address',
        'phone' => 'Guest phone number',
        'check_in_date' => 'Check-in date',
        'check_out_date' => 'Check-out date',
        'num_guests' => 'Number of guests',
        'room_id' => 'Room reference',
        'special_requests' => 'Special requests',
        'total_price' => 'Total price',
        'status' => 'Booking status',
        'created_at' => 'Creation timestamp'
    ];
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Required Column</th><th>Purpose</th><th>Status</th></tr>";
    
    foreach ($requiredColumns as $column => $purpose) {
        $exists = in_array($column, $columnNames);
        $status = $exists ? "✅ EXISTS" : "❌ MISSING";
        $color = $exists ? "green" : "red";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column) . "</td>";
        echo "<td>" . htmlspecialchars($purpose) . "</td>";
        echo "<td style='color: $color; font-weight: bold;'>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check what's missing
    $missingColumns = [];
    foreach ($requiredColumns as $column => $purpose) {
        if (!in_array($column, $columnNames)) {
            $missingColumns[] = $column;
        }
    }
    
    if (empty($missingColumns)) {
        echo "<h3>✅ All Required Columns Exist!</h3>";
        echo "<p>Your database structure is complete. The booking form should work.</p>";
        echo "<p><a href='bookingform.php'>Test Booking Form</a></p>";
    } else {
        echo "<h3>❌ Missing Columns:</h3>";
        echo "<ul>";
        foreach ($missingColumns as $column) {
            echo "<li>" . htmlspecialchars($column) . "</li>";
        }
        echo "</ul>";
        
        echo "<h3>🔧 Quick Fix Commands:</h3>";
        echo "<p>Run these in phpMyAdmin one by one:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
        
        foreach ($missingColumns as $column) {
            switch ($column) {
                case 'phone':
                    echo "ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email;\n";
                    break;
                case 'num_guests':
                    echo "ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;\n";
                    break;
                case 'booking_id':
                    echo "ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id;\n";
                    echo "UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL;\n";
                    echo "ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL;\n";
                    echo "ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id);\n";
                    break;
                default:
                    echo "-- Add $column column as needed\n";
            }
        }
        echo "</pre>";
    }
    
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?> 