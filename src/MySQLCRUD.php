<?php

/**
 * 
 * This file is a part of the MadByAd\MPLMySQL
 * 
 * @author    MadByAd <adityaaw84@gmail.com>
 * @license   MIT License
 * @copyright Copyright (c) MadByAd 2024
 * 
 */

namespace MadByAd\MPLMySQL;

use mysqli;

/**
 * 
 * The MySQLCRUD class is a class that allows you to execute CRUD operation
 * 
 * @author    MadByAd <adityaaw84@gmail.com>
 * @license   MIT License
 * @copyright Copyright (c) MadByAd 2024
 * 
 */

class MySQLCRUD
{

    /**
     * Create / insert a new data into a table
     * 
     * @param string $table      The table name
     * @param array  $columns    The columns to be inserted with data
     * @param array  $values     The values for the columns
     * @param mysqli $connection The mysqli connection if null then it will use the default connection
     * 
     * @return void
     * 
     * @throws MySQLNoConnectionException       If no connection is supplied
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the values to the prepared query
     * @throws ArgumentCountError               if the number of binded values does not match
     *                                          the number of parameters in the prepared query
     */

    public static function create(
        string $table,
        array $columns,
        array $values,
        mysqli $connection = null
    ) {
        
        if($connection == null) {
            $connection = MySQL::getDefaultConnection();
        }

        $table = $table;
        $valueParameter = [];

        foreach($columns as $u) {
            $valueParameter[] = "?"; 
        }

        $columnString = implode(",", $columns);
        $valueParameter = implode(",", $valueParameter);

        $query = new MySQLQuery("INSERT INTO {$table} ({$columnString}) VALUES ({$valueParameter})");
        $query->bind(...$values);
        $query->execute();

    }

    /**
     * Read / Get a data from a table
     * 
     * @param string $table        The table name
     * @param array  $columns      The columns readed / selected
     * @param array  $condition    The condition for the readed / selected columns
     * @param array  $values       The values which will be binded to the condition
     * @param array  $readSettings An associative array containing the additional read setting
     *                             The key you can add for additional setting is
     *                             * `limit` limit the amount of rows to read / select
     *                             * `offset` offset the starting rows to read / select
     *                             * `orderBy` order the rows by a column
     *                             * `orderType` either order the rows by `ascending` or
     *                               `descending` (can also use `asc`, `desc`, `a`, `d`)
     * @param mysqli $connection   The mysqli connection if null then it will use the default connection
     * 
     * @return array The readed / selected rows 
     * 
     * @throws MySQLNoConnectionException       If no connection is supplied
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the values to the prepared query
     * @throws ArgumentCountError               if the number of binded values does not match
     *                                          the number of parameters in the prepared query
     */

    public static function read(
        string $table,
        array $columns = null,
        array $condition = null,
        array $values = null,
        array $readSettings = null,
        mysqli $connection = null
    ) {

        if($condition != null) {
            foreach($condition as $index => $cond) {
                $condition[$index] = "({$cond})";
            }
            $conditionString = implode(" AND ", $condition);
        }

        if($columns == null or $columns === []) {
            $columnString = "*";
        } else {
            $columnString = implode(",", $columns);
        }

        $limit = $readSettings["limit"] ?? null;
        $offset = $readSettings["offset"] ?? null;
        $orderBy = $readSettings["orderBy"] ?? null;
        $orderType = $readSettings["orderType"] ?? "ASC";
        $orderType = strtoupper($orderType);

        // construct the query

        $query = "SELECT {$columnString} FROM {$table} ";

        if($condition != null) {
            $query .= "WHERE {$conditionString} ";
        }

        if($orderBy != null) {
            if($orderType == "ASCENDING" || $orderType == "ASC" || $orderType == "A") {
                $query .= "ORDER BY {$orderBy} ASC ";
            } elseif($orderType == "DESCENDING" || $orderType == "DESC" || $orderType == "D") {
                $query .= "ORDER BY {$orderBy} DESC ";
            }
        }

        if($limit != null) {
            $query .= "LIMIT ? ";
        }

        if($offset != null) {
            $query .= "OFFSET ? ";
        }

        $query = new MySQLQuery($query, $connection);

        if($values != null || $limit != null || $offset != null) {
            
            $valueToBind = [];

            if($values != null) {
                $valueToBind = $values;
            }

            if($limit != null) {
                $valueToBind[] = $limit;
            }

            if($offset != null) {
                $valueToBind[] = $offset;
            }

            $query->bind(...$valueToBind);

        }

        $query->execute();

        return $query->result();

    }

    /**
     * Update a column on a table
     * 
     * @param string $table           The table
     * @param array  $columns         The columns names to be updated
     * @param array  $columnValues    The new values for the updated column
     * @param array  $condition       The condition on which column to be update
     * @param array  $conditionValues The values which will be binded to the condition
     * @param mysqli $connection      The mysqli connection if null then it will use the default connection
     * 
     * @throws MySQLNoConnectionException       If no connection is supplied
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the values to the prepared query
     * @throws ArgumentCountError               if the number of binded values does not match
     *                                          the number of parameters in the prepared query
     */

    public static function update(string $table, array $columns, array $columnValues, array $condition = null, array $conditionValues = null, mysqli $connection = null)
    {

        $columnString = implode(",", $columns);
        
        if($condition != null) {
            foreach($condition as $index => $cond) {
                $condition[$index] = "({$cond})";
            }
            $conditionString = implode(" AND ", $condition);
        }

        $query = "UPDATE {$table} SET {$columnString} ";

        if($condition != null) {
            $query .= "WHERE {$conditionString}";
        }

        $query = new MySQLQuery($query, $connection);

        if($conditionValues != null) {
            $valuesToBind = array_merge($columnValues, $conditionValues);
        } else {
            $valuesToBind = $columnValues;
        }

        $query->bind(...$valuesToBind);

        $query->execute();

    }

    /**
     * Delete a data from a table
     * 
     * @param string $table      The table
     * @param array  $condition  The condition on which data should be deleted
     * @param array  $values     The values which will be binded to the condition
     * @param mysqli $connection The mysqli connection if null then it will use the default connection
     * 
     * @throws MySQLNoConnectionException       If no connection is supplied
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the values to the prepared query
     * @throws ArgumentCountError               if the number of binded values does not match
     *                                          the number of parameters in the prepared query
     */

    public static function delete(string $table, array $condition = null, array $values = null, mysqli $connection = null)
    {

        if($condition != null) {
            foreach($condition as $index => $cond) {
                $condition[$index] = "({$cond})";
            }
            $conditionString = implode(" AND ", $condition);
        }

        $query = "DELETE FROM {$table} ";

        if($condition != null) {
            $query .= "WHERE {$conditionString}";
        }

        $query = new MySQLQuery($query, $connection);

        if($values != null) {
            $query->bind(...$values);
        }

        $query->execute();

    }

}
