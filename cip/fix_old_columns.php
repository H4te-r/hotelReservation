<?php
require_once 'config/database.php';

echo "<h2>Clean Up Old Columns</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Check current structure
    $stmt = $pdo->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll();
    $columnNames = array_column($columns, 'Field');
    
    echo "<h3>Current Columns:</h3>";
    echo "<ul>";
    foreach ($columnNames as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Check if we have duplicate columns
    $hasOldColumns = in_array('guest_email', $columnNames) && in_array('email', $columnNames);
    $hasOldPhone = in_array('guest_phone', $columnNames) && in_array('phone', $columnNames);
    
    if ($hasOldColumns || $hasOldPhone) {
        echo "<h3>🔧 Found old columns that need cleanup...</h3>";
        
        // Copy data from old to new columns if new columns are empty
        if ($hasOldColumns) {
            echo "<p>🔧 Copying data from guest_email to email...</p>";
            $pdo->exec("UPDATE reservations SET email = guest_email WHERE email = '' OR email IS NULL");
            echo "<p>✅ Data copied from guest_email to email</p>";
        }
        
        if ($hasOldPhone) {
            echo "<p>🔧 Copying data from guest_phone to phone...</p>";
            $pdo->exec("UPDATE reservations SET phone = guest_phone WHERE phone = '' OR phone IS NULL");
            echo "<p>✅ Data copied from guest_phone to phone</p>";
        }
        
        // Remove old columns
        if ($hasOldColumns) {
            echo "<p>🔧 Removing old guest_email column...</p>";
            $pdo->exec("ALTER TABLE reservations DROP COLUMN guest_email");
            echo "<p>✅ Removed guest_email column</p>";
        }
        
        if ($hasOldPhone) {
            echo "<p>🔧 Removing old guest_phone column...</p>";
            $pdo->exec("ALTER TABLE reservations DROP COLUMN guest_phone");
            echo "<p>✅ Removed guest_phone column</p>";
        }
        
    } else {
        echo "<p>✅ No old columns found - database is clean!</p>";
    }
    
    // Show final structure
    echo "<h3>Final Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE reservations");
    $finalColumns = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($finalColumns as $column) {
        echo "<li>" . htmlspecialchars($column['Field']) . " (" . htmlspecialchars($column['Type']) . ")</li>";
    }
    echo "</ul>";
    
    echo "<h3>✅ Cleanup completed!</h3>";
    echo "<p>Your booking form should now work correctly.</p>";
    echo "<p><a href='test_booking.php'>Test Booking Form Again</a></p>";
    echo "<p><a href='bookingform.php'>Try Actual Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?> 