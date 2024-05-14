<?php

require_once 'db.php'; // Подключение к файлу с реализацией подключения к базе данных

header('Content-Type: application/json'); // Установка заголовка для ответа в формате JSON

$client_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Получение и валидация параметра id из GET-запроса
if ($client_id === false || $client_id === null) {
    echo json_encode(['error' => 'Invalid client ID']); // Возврат ошибки в случае недействительного id
    exit;
}

try {
    $pdo = Database::getInstance()->getConnection(); // Получение PDO подключения через Singleton
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.second_name, p.title, p.price
        FROM users u
        JOIN user_orders o ON u.id = o.user_id
        JOIN products p ON o.product_id = p.id
        WHERE u.id = :client_id
        ORDER BY p.title ASC, p.price DESC
    ");

    // Подготовка SQL-запроса с объединением таблиц
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT); // Привязка параметра id клиента к запросу
    $stmt->execute(); // Выполнение запроса
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Получение результатов запроса в виде ассоциативного массива

    if (empty($results)) {
        echo json_encode(['error' => 'No orders found for the given client ID']); // Возврат ошибки, если заказы не найдены
    } else {
        echo json_encode($results); // Возврат результатов в формате JSON
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]); // Возврат ошибки в случае исключения
}

