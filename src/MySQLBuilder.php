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

use MadByAd\MPLMySQL\Exceptions\MySQLNoConnectionException;
use MadByAd\MPLMySQL\Exceptions\MySQLQueryFailToExecuteException;
use mysqli;

/**
 * 
 * The MySQLBuilder class is a class that allows you to build querries and execute them
 * 
 * @author    MadByAd <adityaaw84@gmail.com>
 * @license   MIT License
 * @copyright Copyright (c) MadByAd 2024
 * 
 */

class MySQLBuilder
{

    /**
     * Determine whether the query is a select query
     * 
     * @var bool
     */

    private bool $isSelect = false;

    /**
     * Determine on which table shall the select query be executed
     * 
     * @var string
     */

    private string $selectTable = "";

    /**
     * Determine which columns should be selected
     * 
     * @var array
     */

    private array $selectColumn = [];

    /**
     * Determine whether the select query shall have a JOIN clause
     * 
     * @var bool
     */

    private bool $isJoining = false;

    /**
     * The table which will be joined with
     * 
     * @var string
     */

    private string $joinTable = "";

    /**
     * Determine which columns should be selected from the join table
     * 
     * @var array
     */

    private array $joinColumn = [];

    /**
     * Determine the column from the original table which will be used for
     * comparing with the column on the joined table to join
     * 
     * @var string
     */

    private string $columnFromTableUseToJoin = "";

    /**
     * Determine the column from the joined table which will be used for
     * comparing with the column on the original table to join
     * 
     * @var string
     */

    private string $columnFromJoinUseToJoin = "";

    /**
     * Determine whether the query has a condition (WHERE clause)
     * 
     * @var bool
     */

    private bool $hasCondition = false;

    /**
     * Store the condition
     * 
     * @var array
     */

    private array $conditions = [];

    /**
     * Store the condition value which will be binded to the condition
     * 
     * @var array
     */

    private array $conditionValue = [];

    /**
     * Determine the selection limit
     * 
     * @var int
     */

    private int $selectLimit = 0;

    /**
     * Determine the selection offset
     * 
     * @var int
     */

    private int $selectOffset = 0;

    /**
     * Determine on which column should the selection be ordered with
     * 
     * @var string
     */

    private string $orderColumn = "";

    /**
     * Determine whether the order should be ascending (if not then descending)
     * 
     * @var bool
     */

    private bool $orderAscending = true;

    /**
     * Determine whether the query is an update query
     * 
     * @var bool
     */

    private bool $isUpdate = false;

    /**
     * The table which the update query will be executed
     * 
     * @var string
     */

    private string $updateTable = "";

    /**
     * Determine which columns should be updated
     * 
     * @var array
     */

    private array $updateColumn = [];

    /**
     * Determine the update / new value
     * 
     * @var array
     */

    private array $updateValues = [];

    /**
     * Determine whether the query is a delete query
     * 
     * @var bool
     */

    private bool $isDelete = false;

    /**
     * The table which the delete query will be executed
     * 
     * @var string
     */

    private string $deleteTable = "";

    /**
     * Store the mysqli connection
     * 
     * @var mysqli
     */

    private mysqli $connection;

    /**
     * Construct a new mysql builder
     * 
     * @param mysqli|MySQL|null $connection The mysql connection if null then it will use
     *                                      the default connection on the MySQL class
     * 
     * @throws MySQLNoConnectionException If no connection is supplied
     */
    
    public function __construct(mysqli|MySQL|null $connection = null)
    {
        if($connection == null) {
            $this->connection = MySQL::getDefaultConnection();
        } elseif ($connection instanceof MySQL) {
            $this->connection = $connection->getConnection();
        } else {
            $this->connection = $connection;
        }

        if($this->connection == null) {
            throw new MySQLNoConnectionException("Error: no mysql connection is supplied");
        }
    }

    /**
     * Perform an insert query
     * 
     * @param string $table   The table which the query will be executed on
     * @param array  $columns The list of columns that will be inserted with data / values
     * @param array  $values  The list of values for the columns
     * 
     * @return void
     * 
     * @throws ArgumentCountError               if the number of binded values
     *                                          does not match the number of
     *                                          parameters in the prepared query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the
     *                                          values to the prepared query
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     */

    public function queryInsert(string $table, array $columns, array $values)
    {
        
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
     * Construct a select query
     * 
     * @param string $table   The table which the select query will be executed on
     * @param array  $columns The list of columns that should be selected / get the data
     * 
     * @return self
     */

    public function querySelect(string $table, array $columns = [])
    {
        $this->reset();
        $this->isSelect = true;
        $this->selectTable = $table;
        $this->selectColumn = $columns;
        return $this;
    }

    /**
     * Add a join clause to the query (only for select query)
     * 
     * @param string $tableToJoin     The table name which will be joined
     * @param array  $columns         The list of columns from the joined table
     *                                that should be selected / get the data
     * @param string $columnFromTable the column from the original table which will be used for
     *                                comparing with the column on the joined table to join
     * @param string $columnFromJoin  the column from the joined table which will be used for
     *                                comparing with the column on the original table to join
     * @return self
     */

    public function join(string $tableToJoin, array $columns = [], string $columnFromTable = "", string $columnFromJoin = "")
    {
        $this->isJoining = true;
        $this->joinTable = $tableToJoin;
        $this->joinColumn = $columns;
        $this->columnFromTableUseToJoin = $columnFromTable;
        $this->columnFromJoinUseToJoin = $columnFromJoin;
        return $this;
    }

    /**
     * Add a condition (where clause) to the query
     * 
     * @param array|string           $conditions The condition or a list of conditions
     * @param array|string|int|float $values     The value for the condition or a list
     *                                           of values for the conditions
     * @return self
     */

    public function condition(array|string $conditions, array|string|int|float $values)
    {

        if(!is_array($conditions)) {
            $conditions = [$conditions];
        }

        if(!is_array($values)) {
            $values = [$values];
        }
        
        $this->hasCondition = true;
        $this->conditions[] = $conditions;

        foreach($values as $value) {
            $this->conditionValue[] = $value;
        }

        return $this;

    }

    /**
     * Limit the selection amount
     * 
     * @param int $limit The limit
     * 
     * @return self
     */

    public function limit(int $limit)
    {
        $this->selectLimit = $limit;
        return $this;
    }

    /**
     * Offset the selection
     * 
     * @param int $limit The limit
     * 
     * @return self
     */

    public function offset(int $offset)
    {
        $this->selectOffset = $offset;
        return $this;
    }

    /**
     * Order the selection with a column
     * 
     * @param string $column    The column which will be used for ordering the selection
     * @param bool   $ascending if `TRUE` then it will be ordered ascendingly else it will
     *                          ordered descend
     * @return self
     */

    public function orderBy(string $column, bool $ascending = true)
    {
        $this->orderColumn = $column;
        $this->orderAscending = $ascending;
        return $this;
    }

    /**
     * Construct an update query
     * 
     * @param string $table The table which the update query will be executed on
     * 
     * @return self
     */

    public function queryUpdate(string $table)
    {
        $this->reset();
        $this->isUpdate = true;
        $this->updateTable = $table;
        return $this;
    }

    /**
     * Set a new value for the given column
     * 
     * @param string                 $column
     * @param array|string|int|float $value  The new value
     * 
     * @return self
     */

    public function set(string $column, array|string|int|float $value)
    {
        $this->updateColumn[] = $column;
        $this->updateValues[] = $value;
        return $this;
    }

    /**
     * Construct a delete query
     * 
     * @param string $table The table which the delete query will be executed on
     * 
     * @return self
     */

    public function queryDelete(string $table)
    {
        $this->reset();
        $this->isDelete = true;
        $this->deleteTable = $table;
        return $this;
    }

    /**
     * Execute the constructed query
     * 
     * @return void|array nothing or an associative array containing the list of selected rows
     *                    if it is a select query
     * 
     * @throws ArgumentCountError               if the number of binded values
     *                                          does not match the number of
     *                                          parameters in the prepared query
     * @throws MySQLQueryFailToBindException    if somehow failed to bind the
     *                                          values to the prepared query
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     */

    public function execute()
    {
        if($this->isSelect) {
            return $this->executeSelect();
        }
        if($this->isUpdate) {
            $this->executeUpdate();
        }
        if($this->isDelete) {
            $this->executeDelete();
        }
        return null;
    }

    /**
     * Execute the select query
     * 
     * @return array nothing or an associative array containing the list of
     *               selected rows if it is a select query
     */

    private function executeSelect()
    {
        
        $table = $this->selectTable;
        $selectColumn = $this->selectColumn;

        if($selectColumn == []) {
            $selection = "{$table}.*";
        } else {
            $selection = [];
            foreach($selectColumn as $column) {
                $selection[] = "{$table}.{$column}";
            }
            $selection = implode(",", $selection);
        }

        $query = "";

        if($this->isJoining) {
            
            $joinTable = $this->joinTable;
            $joinColumn = $this->joinColumn;

            if($joinColumn == []) {
                $joinSelection = "{$joinTable}.*";
            } else {
                $joinSelection = [];
                foreach($joinColumn as $column) {
                    $joinSelection[] = "{$joinTable}.{$column}";
                }
                $joinSelection = implode(",", $joinSelection);
            }

            $selection = $selection . "," . $joinSelection;

            $columnFromJoinUseToJoin = $this->columnFromJoinUseToJoin;
            $columnFromTableUseToJoin = $this->columnFromTableUseToJoin;

            $query = "SELECT {$selection} FROM {$table} JOIN {$joinTable} ON {$table}.{$columnFromTableUseToJoin} = {$joinTable}.{$columnFromJoinUseToJoin} ";

        } else {
            $query = "SELECT {$selection} FROM {$table} ";
        }

        $valuesToBind = [];

        if($this->hasCondition) {
            
            $conditions = $this->conditions;
            $conditionValue = $this->conditionValue;
            $conditionQuery = [];

            foreach($conditionValue as $value) {
                $valuesToBind[] = $value;
            }

            foreach($conditions as $condition) {
                $conditionQuery[] = "(" . implode(" AND ", $condition) . ")";
            }

            $conditionQuery = implode(" OR ", $conditionQuery);

            $query .= "WHERE {$conditionQuery} ";

        }

        if($this->orderColumn != "") {
            
            $orderColumn = $this->orderColumn;

            if($this->orderAscending) {
                $query .= "ORDER BY {$orderColumn} ASC ";
            } else {
                $query .= "ORDER BY {$orderColumn} DESC ";
            }

        }

        if($this->selectLimit != 0) {
            $query .= "LIMIT ? ";
            $valuesToBind[] = $this->selectLimit;
        }

        if($this->selectOffset != 0) {
            $query .= "OFFSET ? ";
            $valuesToBind[] = $this->selectOffset;
        }

        $query = new MySQLQuery($query, $this->connection);

        if($valuesToBind != []) {
            $query->bind(...$valuesToBind);
        }
        
        $query->execute();

        $this->reset();
    
        return $query->result();

    }

    /**
     * Execute the update query
     * 
     * @return void
     */

    private function executeUpdate()
    {

        $table = $this->updateTable;
        $updateColumn = $this->updateColumn;
        $updateValues = $this->updateValues;

        if($updateColumn == []) {
            throw new MySQLQueryFailToExecuteException("Error: There is nothing to update");
        }

        $updateColumn = implode(",", $updateColumn);
        $valuesToBind = $updateValues;

        $query = "UPDATE {$table} SET {$updateColumn} ";

        if($this->hasCondition) {
            
            $conditions = $this->conditions;
            $conditionValue = $this->conditionValue;
            $conditionQuery = [];

            foreach($conditionValue as $value) {
                $valuesToBind[] = $value;
            }

            foreach($conditions as $condition) {
                $conditionQuery[] = "(" . implode(" AND ", $condition) . ")";
            }

            $conditionQuery = implode(" OR ", $conditionQuery);

            $query .= " WHERE {$conditionQuery} ";

        }

        $query = new MySQLQuery($query, $this->connection);

        $query->bind(...$valuesToBind);

        $query->execute();

        $this->reset();

    }

    /**
     * Execute the delete query
     * 
     * @return void
     */

    private function executeDelete()
    {

        $table = $this->deleteTable;
        $valuesToBind = [];

        $query = "DELETE FROM {$table} ";

        if($this->hasCondition) {
            
            $conditions = $this->conditions;
            $conditionValue = $this->conditionValue;
            $conditionQuery = [];

            foreach($conditionValue as $value) {
                $valuesToBind[] = $value;
            }

            foreach($conditions as $condition) {
                $conditionQuery[] = "(" . implode(" AND ", $condition) . ")";
            }

            $conditionQuery = implode(" OR ", $conditionQuery);

            $query .= "WHERE {$conditionQuery} ";

        }

        $query = new MySQLQuery($query, $this->connection);

        if($valuesToBind != []) {
            $query->bind(...$valuesToBind);
        }

        $query->execute();

        $this->reset();

    }

    /**
     * reset value for a new query
     * 
     * @return void
     */

    private function reset()
    {
        $this->isSelect = false;
        $this->selectTable = "";
        $this->selectColumn = [];
        $this->isJoining = false;
        $this->joinTable = "";
        $this->joinColumn = [];
        $this->columnFromTableUseToJoin = "";
        $this->columnFromJoinUseToJoin = "";
        $this->hasCondition = false;
        $this->conditions = [];
        $this->conditionValue = [];
        $this->selectLimit = 0;
        $this->selectOffset = 0;
        $this->orderColumn = "";
        $this->orderAscending = true;
        $this->isUpdate = false;
        $this->updateTable = "";
        $this->updateColumn = [];
        $this->updateValues = [];
        $this->isDelete = false;
        $this->deleteTable = "";
    }

}
