<?php
function getPdoConnection() {
    $host = 'products_mysql_1';
    $dbname = 'testdb';
    $username = 'testdb';
    $password = 'testdb';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("DB Connection failed: " . $e->getMessage());
    }
}

