<?php

require_once 'db.php'; // Подключение к файлу с реализацией подключения к базе данных

header('Content-Type: application/json'); // Установка заголовка для ответа в формате JSON

$input = file_get_contents('php://input'); // Получение JSON данных из POST-запроса
$data = json_decode($input, true); // Декодирование JSON данных в ассоциативный массив

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    echo json_encode(['error' => 'Invalid JSON input']); // Возврат ошибки в случае недействительных данных
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection(); // Получение PDO подключения через Singleton
    $stmt = $pdo->prepare("INSERT INTO products (title, price) VALUES (:title, :price)"); // Подготовка SQL-запроса для вставки данных

    $pdo->beginTransaction(); // Начало транзакции
    foreach ($data as $product) {
        $title = filter_var($product['title'], FILTER_SANITIZE_STRING); // Санитизация и валидация данных
        $price = filter_var($product['price'], FILTER_VALIDATE_FLOAT);

        if ($title === false || $price === false) {
            throw new Exception('Invalid product data'); // Исключение в случае недействительных данных
        }

        $stmt->bindParam(':title', $title, PDO::PARAM_STR); // Привязка параметров к запросу
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->execute(); // Выполнение запроса
    }
    $pdo->commit(); // Фиксация транзакции

    echo json_encode(['status' => 'success']); // Возврат успешного ответа
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Откат транзакции в случае ошибки
    }
    echo json_encode(['error' => $e->getMessage()]); // Возврат ошибки
}

