<?php
class Database
{
    private static ?PDO $instance = null;
    private static ?array $config = null;

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            if (self::$config === null) {
                throw new Exception('Database config not set. Call Database::setConfig($config) first.');
            }
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$config['server'],
                self::$config['name'],
                self::$config['charset']
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
                PDO::ATTR_AUTOCOMMIT         => true,
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
                PDO::ATTR_CASE               => PDO::CASE_NATURAL,
            ];
            $pdo = new PDO($dsn, self::$config['user'], self::$config['pass'], $options);
            $pdo->exec("SET time_zone = '" . self::$config['timezone'] . "'");
            self::$instance = $pdo;
        }
        return self::$instance;
    }
}
