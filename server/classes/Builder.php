<?php

class Builder extends Database
{
   private static ?int $rowcount = null;
   private static ?int $columncount = null;

   private static function isSelectQuery(string $sql): bool
   {
      $sql = strtolower(trim($sql));
      return str_starts_with($sql, 'select');
   }

   private static function build(string $sql, array $params = []): PDOStatement
   {
      $conn = parent::getInstance();
      $stmt = $conn->prepare($sql);
      $stmt->execute($params);

      if (self::isSelectQuery($sql)) {
         self::$rowcount = $stmt->rowCount();
         self::$columncount = $stmt->columnCount();
      } else {
         self::$rowcount = null;
         self::$columncount = null;
      }

      return $stmt;
   }

   public static function execute(string $sql, array $params = []): PDOStatement
   {
      return self::build($sql, $params);
   }


   public static function getQuery(string $sql, array $params = []): array
   {
      if (!self::isSelectQuery($sql)) {
         return [];
      }

      return self::execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC) ?: [];
   }

   public static function findQuery(string $sql, array $params = []): array
   {
      if (!self::isSelectQuery($sql)) {
         return [];
      }

      return self::execute($sql, $params)->fetch(PDO::FETCH_ASSOC) ?: [];
   }



   public static function countQuery(string $sql, array $params = []): ?int
   {
      self::execute($sql, $params);
      return self::$rowcount;
   }

   public static function columnCount(string $sql, array $params = []): ?int
   {
      self::execute($sql, $params);
      return self::$columncount;
   }

   public static function tableExist(string $table): bool
   {
      $result = self::execute("SHOW TABLES LIKE ?", [$table]);
      return $result->rowCount() > 0;
   }

   public static function columnExist(string $table, string $column): bool
   {
      $result = self::execute("SHOW COLUMNS FROM `$table` LIKE ?", [$column]);
      return $result->rowCount() > 0;
   }
}
