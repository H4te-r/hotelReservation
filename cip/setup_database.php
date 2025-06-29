<?php
echo "<h2>Database Setup</h2>";

try {
    // Connect without specifying database first
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "<p>✅ Connected to MySQL server</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'hotel_reservation_system'");
    $dbExists = $stmt->fetch();
    
    if (!$dbExists) {
        echo "<p>🔧 Creating database...</p>";
        $pdo->exec("CREATE DATABASE hotel_reservation_system");
        echo "<p>✅ Database created</p>";
    } else {
        echo "<p>✅ Database already exists</p>";
    }
    
    // Connect to the specific database
    $pdo = new PDO(
        "mysql:host=localhost;dbname=hotel_reservation_system;charset=utf8",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "<p>✅ Connected to hotel_reservation_system database</p>";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    $tableNames = array_column($tables, array_keys($tables[0])[0]);
    
    echo "<h3>Current tables:</h3>";
    if (empty($tableNames)) {
        echo "<p>❌ No tables found</p>";
    } else {
        echo "<ul>";
        foreach ($tableNames as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    // Create tables if they don't exist
    if (!in_array('hotel', $tableNames)) {
        echo "<p>🔧 Creating hotel table...</p>";
        $pdo->exec("
            CREATE TABLE hotel (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                address TEXT NOT NULL,
                phone VARCHAR(20),
                email VARCHAR(255),
                description TEXT,
                rating DECIMAL(2,1) DEFAULT 0.0,
                image_url VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "<p>✅ Hotel table created</p>";
    }
    
    if (!in_array('rooms', $tableNames)) {
        echo "<p>🔧 Creating rooms table...</p>";
        $pdo->exec("
            CREATE TABLE rooms (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                room_number VARCHAR(50) NOT NULL UNIQUE,
                room_type VARCHAR(100) NOT NULL,
                capacity INT NOT NULL,
                size INT DEFAULT 300,
                price_per_night DECIMAL(10,2) NOT NULL,
                description TEXT,
                has_wifi BOOLEAN DEFAULT TRUE,
                has_tv BOOLEAN DEFAULT TRUE,
                has_ac BOOLEAN DEFAULT TRUE,
                is_available BOOLEAN DEFAULT TRUE,
                image_url VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "<p>✅ Rooms table created</p>";
    }
    
    if (!in_array('reservations', $tableNames)) {
        echo "<p>🔧 Creating reservations table...</p>";
        $pdo->exec("
            CREATE TABLE reservations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_id VARCHAR(50) UNIQUE NOT NULL,
                room_id INT NOT NULL,
                guest_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                check_in_date DATE NOT NULL,
                check_out_date DATE NOT NULL,
                num_guests INT NOT NULL,
                total_price DECIMAL(10,2),
                status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
                special_requests TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
            )
        ");
        echo "<p>✅ Reservations table created</p>";
    }
    
    if (!in_array('admin_users', $tableNames)) {
        echo "<p>🔧 Creating admin_users table...</p>";
        $pdo->exec("
            CREATE TABLE admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                role ENUM('admin', 'manager') DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "<p>✅ Admin_users table created</p>";
    }
    
    // Insert sample data if tables are empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM hotel");
    $hotelCount = $stmt->fetch()['count'];
    
    if ($hotelCount == 0) {
        echo "<p>🔧 Adding sample hotel data...</p>";
        $pdo->exec("
            INSERT INTO hotel (name, address, phone, email, description, rating, image_url) VALUES
            ('Grand Plaza Hotel', '123 Main Street, Downtown, City', '+1-555-0123', 'info@grandplaza.com', 'Luxury hotel in the heart of downtown with stunning city views and world-class amenities.', 4.5, 'hotel1.jpg')
        ");
        echo "<p>✅ Sample hotel data added</p>";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM rooms");
    $roomCount = $stmt->fetch()['count'];
    
    if ($roomCount == 0) {
        echo "<p>🔧 Adding sample room data...</p>";
        $pdo->exec("
            INSERT INTO rooms (name, room_number, room_type, capacity, size, price_per_night, description, has_wifi, has_tv, has_ac, image_url) VALUES
            ('Standard Room 101', '101', 'Standard Room', 2, 300, 150.00, 'Comfortable standard room with city view, queen bed, and modern amenities', TRUE, TRUE, TRUE, 'room1.jpg'),
            ('Deluxe Room 201', '201', 'Deluxe Room', 3, 450, 250.00, 'Spacious deluxe room with premium amenities, king bed, and city skyline view', TRUE, TRUE, TRUE, 'room3.jpg'),
            ('Suite 301', '301', 'Suite', 4, 600, 400.00, 'Luxury suite with separate living area, king bed, and panoramic city views', TRUE, TRUE, TRUE, 'room5.jpg')
        ");
        echo "<p>✅ Sample room data added</p>";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $adminCount = $stmt->fetch()['count'];
    
    if ($adminCount == 0) {
        echo "<p>🔧 Adding admin user...</p>";
        $pdo->exec("
            INSERT INTO admin_users (username, password, email, full_name, role) VALUES
            ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotel.com', 'System Administrator', 'admin')
        ");
        echo "<p>✅ Admin user added (username: admin, password: password)</p>";
    }
    
    echo "<h3>✅ Database setup completed!</h3>";
    echo "<p>Your hotel reservation system is now ready.</p>";
    echo "<p><a href='simple_check.php'>Check Database Structure</a></p>";
    echo "<p><a href='bookingform.php'>Test Booking Form</a></p>";
    echo "<p><a href='index.php'>← Back to Hotel Website</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure MySQL is running and the credentials are correct.</p>";
}
?> 