<?php
require_once 'config/database.php';

echo "<h2>Database Structure Check</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Check reservations table structure
    echo "<h3>Current Reservations Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if email column exists
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
    
    echo "<h3>Column Status:</h3>";
    echo "<p>Email column exists: " . ($emailExists ? "✅ YES" : "❌ NO") . "</p>";
    echo "<p>Guest_email column exists: " . ($guestEmailExists ? "✅ YES" : "❌ NO") . "</p>";
    
    if (!$emailExists && $guestEmailExists) {
        echo "<h3>🔧 Fix Needed:</h3>";
        echo "<p>The 'guest_email' column needs to be renamed to 'email'.</p>";
        echo "<p><a href='fix_email_column.php'>Click here to fix this automatically</a></p>";
    } elseif (!$emailExists && !$guestEmailExists) {
        echo "<h3>🔧 Fix Needed:</h3>";
        echo "<p>No email column found. Need to add it.</p>";
        echo "<p><a href='fix_email_column.php'>Click here to fix this automatically</a></p>";
    } else {
        echo "<h3>✅ Email column is ready!</h3>";
    }
    
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?> 