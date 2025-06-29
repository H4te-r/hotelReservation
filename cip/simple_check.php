<?php
echo "<h2>Simple Database Check</h2>";

try {
    // Include database connection
    require_once 'config/database.php';
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Check if reservations table exists
    echo "<h3>Step 1: Check if reservations table exists</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'reservations'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "<p>✅ Reservations table exists</p>";
    } else {
        echo "<p>❌ Reservations table does not exist!</p>";
        echo "<p>You need to create the database first.</p>";
        exit;
    }
    
    // Show all columns in reservations table
    echo "<h3>Step 2: Current columns in reservations table</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM reservations");
    $columns = $stmt->fetchAll();
    
    if (empty($columns)) {
        echo "<p>❌ No columns found in reservations table</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check for specific required columns
    echo "<h3>Step 3: Check required columns</h3>";
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['email', 'phone', 'num_guests', 'booking_id'];
    
    foreach ($requiredColumns as $required) {
        if (in_array($required, $columnNames)) {
            echo "<p>✅ $required column exists</p>";
        } else {
            echo "<p>❌ $required column is MISSING</p>";
        }
    }
    
    // Show what needs to be fixed
    $missing = array_diff($requiredColumns, $columnNames);
    
    if (empty($missing)) {
        echo "<h3>✅ All required columns exist!</h3>";
        echo "<p>Your database should work with the booking form.</p>";
    } else {
        echo "<h3>❌ Missing columns that need to be added:</h3>";
        echo "<ul>";
        foreach ($missing as $column) {
            echo "<li>$column</li>";
        }
        echo "</ul>";
        
        echo "<h3>🔧 SQL commands to fix:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
        foreach ($missing as $column) {
            switch ($column) {
                case 'phone':
                    echo "ALTER TABLE reservations ADD COLUMN phone VARCHAR(20) AFTER email;\n";
                    break;
                case 'num_guests':
                    echo "ALTER TABLE reservations ADD COLUMN num_guests INT NOT NULL DEFAULT 1 AFTER check_out_date;\n";
                    break;
                case 'booking_id':
                    echo "ALTER TABLE reservations ADD COLUMN booking_id VARCHAR(50) AFTER id;\n";
                    break;
            }
        }
        echo "</pre>";
    }
    
    echo "<p><a href='bookingform.php'>Test Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>This might mean:</p>";
    echo "<ul>";
    echo "<li>Database doesn't exist</li>";
    echo "<li>Database connection failed</li>";
    echo "<li>Wrong database credentials</li>";
    echo "</ul>";
}
?> 