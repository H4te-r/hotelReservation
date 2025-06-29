<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['hotel_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Hotel ID is required']);
    exit;
}

$hotelId = (int)$_GET['hotel_id'];

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT r.*, h.name as hotel_name 
        FROM rooms r 
        JOIN hotels h ON r.hotel_id = h.id 
        WHERE r.hotel_id = ? AND r.is_available = 1
        ORDER BY r.price_per_night
    ");
    $stmt->execute([$hotelId]);
    $rooms = $stmt->fetchAll();
    
    echo json_encode($rooms);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 