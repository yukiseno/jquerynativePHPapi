<?php

class MySQLDatabase implements DatabaseAdapter
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getCurrentTimestampFunction()
    {
        return 'NOW()';
    }

    public function getInsertIgnoreSql($table, $columns)
    {
        $columnList = implode(', ', array_keys($columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        return "INSERT IGNORE INTO $table ($columnList) VALUES ($placeholders)";
    }

    public function insertIgnore($table, $columns, $values)
    {
        $sql = $this->getInsertIgnoreSql($table, $columns);
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($values);
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function exec($sql)
    {
        return $this->connection->exec($sql);
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollBack()
    {
        return $this->connection->rollBack();
    }
}
