<?php

namespace App\Entity\Database;

use PDO;

class Database
{
  private static $instances = [];

  private $connection;

  private function __construct()
  {
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $dbname = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $host = $_ENV['DB_HOST'];

    $this->connection = new PDO("mysql:host={$host};dbname={$dbname}", $user, $password, $options);
    $this->connection->exec('SET CHARACTER SET UTF8');
  }

  public static function getInstance()
  {
    if (!self::$instances) {
      self::$instances = new Database();
    }

    return self::$instances;
  }

  public function getConnection() : PDO
  {
    return $this->connection;
  }
}
