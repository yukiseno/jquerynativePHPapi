<?php

/**
 * Database Adapter Interface
 * Defines the contract for database-specific implementations
 */
interface DatabaseAdapter
{
    /**
     * Get the current timestamp SQL function
     */
    public function getCurrentTimestampFunction();

    /**
     * Get INSERT OR IGNORE SQL syntax for this database
     */
    public function getInsertIgnoreSql($table, $columns);

    /**
     * Execute an INSERT OR IGNORE statement
     */
    public function insertIgnore($table, $columns, $values);

    /**
     * Get the last inserted ID
     */
    public function lastInsertId();

    /**
     * Prepare a statement
     */
    public function prepare($sql);

    /**
     * Execute a statement directly
     */
    public function exec($sql);

    /**
     * Begin transaction
     */
    public function beginTransaction();

    /**
     * Commit transaction
     */
    public function commit();

    /**
     * Rollback transaction
     */
    public function rollBack();
}
