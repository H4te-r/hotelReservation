<?php
require_once 'config/database.php';

echo "<h2>Database Migration Tool</h2>";

try {
    $pdo = getDBConnection();
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Read and execute the migration SQL
    $migration_sql = file_get_contents('update_database.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $migration_sql)));
    
    echo "<h3>Executing database updates...</h3>";
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            try {
                $pdo->exec($statement);
                echo "<p>✅ Executed: " . substr($statement, 0, 50) . "...</p>";
            } catch (Exception $e) {
                echo "<p>⚠️ Warning: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h3>✅ Database migration completed!</h3>";
    echo "<p>Your database has been updated with the missing columns.</p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure your database exists and the connection details are correct.</p>";
}
?> 