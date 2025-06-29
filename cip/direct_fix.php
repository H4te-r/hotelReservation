<?php
require_once 'config/database.php';

echo "<h2>Direct Database Fix</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // First, let's see what columns actually exist
    echo "<h3>Current Reservations Table Columns:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM reservations");
    $columns = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>" . htmlspecialchars($column['Field']) . " (" . htmlspecialchars($column['Type']) . ")</li>";
    }
    echo "</ul>";
    
    // Check what we need to fix
    $columnNames = array_column($columns, 'Field');
    
    echo "<h3>Fixing Database Structure...</h3>";
    
    // Fix 1: Handle email column
    if (in_array('guest_email', $columnNames) && !in_array('email', $columnNames)) {
        echo "<p>🔧 Renaming guest_email to email...</p>";
        $pdo->exec("ALTER TABLE reservations CHANGE COLUMN guest_email email VARCHAR(255) NOT NULL");
        echo "<p>✅ Renamed guest_email to email</p>";
    } elseif (!in_array('email', $columnNames)) {
        echo "<p>🔧 Adding email column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN email VARCHAR(255) NOT NULL AFTER guest_name");
        echo "<p>✅ Added email column</p>";
    } else {
        echo "<p>✅ Email column already exists</p>";
    }
    
    // Fix 2: Handle phone column
    if (in_array('guest_phone', $columnNames) && !in_array('phone', $columnNames)) {
        echo "<p>🔧 Renaming guest_phone to phone...</p>";
        $pdo->exec("ALTER TABLE reservations CHANGE COLUMN guest_phone phone VARCHAR(20)");
        echo "<p>✅ Renamed guest_phone to phone</p>";
    } elseif (!in_array('phone', $columnNames)) {
        echo "<p>🔧 Adding phone column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email");
        echo "<p>✅ Added phone column</p>";
    } else {
        echo "<p>✅ Phone column already exists</p>";
    }
    
    // Fix 3: Add booking_id if missing
    if (!in_array('booking_id', $columnNames)) {
        echo "<p>🔧 Adding booking_id column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id");
        echo "<p>✅ Added booking_id column</p>";
    } else {
        echo "<p>✅ Booking_id column already exists</p>";
    }
    
    // Fix 4: Add num_guests if missing
    if (!in_array('num_guests', $columnNames)) {
        echo "<p>🔧 Adding num_guests column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date");
        echo "<p>✅ Added num_guests column</p>";
    } else {
        echo "<p>✅ Num_guests column already exists</p>";
    }
    
    // Generate booking IDs for existing reservations
    echo "<p>🔧 Generating booking IDs for existing reservations...</p>";
    $pdo->exec("UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL OR booking_id = ''");
    echo "<p>✅ Generated booking IDs</p>";
    
    // Make booking_id NOT NULL and add unique constraint
    echo "<p>🔧 Making booking_id NOT NULL and unique...</p>";
    try {
        $pdo->exec("ALTER TABLE reservations MODIFY COLUMN booking_id VARCHAR(50) NOT NULL");
        $pdo->exec("ALTER TABLE reservations ADD UNIQUE KEY unique_booking_id (booking_id)");
        echo "<p>✅ Made booking_id NOT NULL and unique</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Note: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>✅ Database fix completed!</h3>";
    echo "<p>Your booking form should now work correctly.</p>";
    
    // Show final structure
    echo "<h3>Final Reservations Table Structure:</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM reservations");
    $finalColumns = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($finalColumns as $column) {
        echo "<li>" . htmlspecialchars($column['Field']) . " (" . htmlspecialchars($column['Type']) . ")</li>";
    }
    echo "</ul>";
    
    echo "<p><a href='bookingform.php'>Test Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?> 