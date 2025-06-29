<?php
require_once 'config/database.php';

echo "<h2>Email Column Fix</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll();
    
    $emailExists = false;
    $guestEmailExists = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'email') {
            $emailExists = true;
        }
        if ($column['Field'] === 'guest_email') {
            $guestEmailExists = true;
        }
    }
    
    echo "<h3>Current Status:</h3>";
    echo "<p>Email column exists: " . ($emailExists ? "✅ YES" : "❌ NO") . "</p>";
    echo "<p>Guest_email column exists: " . ($guestEmailExists ? "✅ YES" : "❌ NO") . "</p>";
    
    if ($emailExists) {
        echo "<h3>✅ Email column already exists!</h3>";
        echo "<p>No fix needed for email column.</p>";
    } elseif ($guestEmailExists) {
        echo "<h3>🔧 Renaming guest_email to email...</h3>";
        
        // Rename guest_email to email
        $pdo->exec("ALTER TABLE reservations CHANGE COLUMN guest_email email VARCHAR(255) NOT NULL");
        
        echo "<p>✅ Successfully renamed 'guest_email' to 'email'</p>";
    } else {
        echo "<h3>🔧 Adding email column...</h3>";
        
        // Add email column
        $pdo->exec("ALTER TABLE reservations ADD COLUMN email VARCHAR(255) NOT NULL AFTER guest_name");
        
        echo "<p>✅ Successfully added 'email' column</p>";
    }
    
    // Check for other missing columns
    $bookingIdExists = false;
    $numGuestsExists = false;
    $phoneExists = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'booking_id') {
            $bookingIdExists = true;
        }
        if ($column['Field'] === 'num_guests') {
            $numGuestsExists = true;
        }
        if ($column['Field'] === 'phone') {
            $phoneExists = true;
        }
    }
    
    echo "<h3>Other Required Columns:</h3>";
    echo "<p>Booking_id exists: " . ($bookingIdExists ? "✅ YES" : "❌ NO") . "</p>";
    echo "<p>Num_guests exists: " . ($numGuestsExists ? "✅ YES" : "❌ NO") . "</p>";
    echo "<p>Phone exists: " . ($phoneExists ? "✅ YES" : "❌ NO") . "</p>";
    
    // Add missing columns
    if (!$bookingIdExists) {
        echo "<p>🔧 Adding booking_id column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id");
        echo "<p>✅ Added booking_id column</p>";
    }
    
    if (!$numGuestsExists) {
        echo "<p>🔧 Adding num_guests column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date");
        echo "<p>✅ Added num_guests column</p>";
    }
    
    if (!$phoneExists) {
        echo "<p>🔧 Adding phone column...</p>";
        $pdo->exec("ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email");
        echo "<p>✅ Added phone column</p>";
    }
    
    // Generate booking IDs for existing reservations if needed
    if ($bookingIdExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM reservations WHERE booking_id IS NULL");
        $nullCount = $stmt->fetch()['count'];
        
        if ($nullCount > 0) {
            echo "<p>🔧 Generating booking IDs for existing reservations...</p>";
            $pdo->exec("UPDATE reservations SET booking_id = CONCAT('BK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 3, '0')) WHERE booking_id IS NULL");
            echo "<p>✅ Generated booking IDs</p>";
        }
    }
    
    echo "<h3>✅ Database fix completed!</h3>";
    echo "<p>Your booking form should now work correctly.</p>";
    echo "<p><a href='bookingform.php'>Test Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?> 