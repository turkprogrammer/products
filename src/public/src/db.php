<?php

class Database {
    private static ?Database $instance = null; // Единственный экземпляр класса
    private PDO $pdo; // PDO объект для подключения к базе данных

    // Приватный конструктор, чтобы предотвратить создание экземпляров извне
    private function __construct() {
        $host = 'products_mysql_1';
        $dbname = 'testdb';
        $username = 'testdb';
        $password = 'testdb';

        try {
            // Создание нового PDO объекта для подключения к базе данных
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            // Установка режима ошибок для PDO
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Завершение работы скрипта в случае ошибки подключения
            die("DB Connection failed: " . $e->getMessage());
        }
    }

    // Метод для получения единственного экземпляра класса
    public static function getInstance(): ?Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Метод для получения PDO объекта
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Запрещаем клонирование экземпляра
    private function __clone() {}
    // Запрещаем десериализацию экземпляра
    private function __wakeup() {}
}

