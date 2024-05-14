<?php

require_once 'db.php';

header('Content-Type: application/json');

$client_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($client_id === false || $client_id === null) {
    echo json_encode(['error' => 'Invalid client ID']);
    exit;
}

try {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.second_name, p.title, p.price
        FROM users u
        JOIN user_orders o ON u.id = o.user_id
        JOIN products p ON o.product_id = p.id
        WHERE u.id = :client_id
        ORDER BY p.title ASC, p.price DESC
    ");
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        echo json_encode(['error' => 'No orders found for the given client ID']);
    } else {
        echo json_encode($results);
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    // Экранируем специальные символы для JSON
    $encoded_error_message = json_encode($error_message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    echo json_encode(['error' => $encoded_error_message]);
}

