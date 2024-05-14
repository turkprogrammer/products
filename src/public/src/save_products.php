<?php

require_once 'db.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

try {
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("INSERT INTO products (title, price) VALUES (:title, :price)");

    $pdo->beginTransaction();
    foreach ($data as $product) {
        $title = filter_var($product['title'], FILTER_SANITIZE_STRING);
        $price = filter_var($product['price'], FILTER_VALIDATE_FLOAT);

        if ($title === false || $price === false) {
            throw new Exception('Invalid product data');
        }

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->execute();
    }
    $pdo->commit();

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['error' => $e->getMessage()]);
}

