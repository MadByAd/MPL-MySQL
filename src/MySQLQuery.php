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

use ArgumentCountError;
use MadByAd\MPLMySQL\Exceptions\MySQLNoConnectionException;
use MadByAd\MPLMySQL\Exceptions\MySQLQueryFailToBindException;
use MadByAd\MPLMySQL\Exceptions\MySQLQueryFailToExecuteException;
use mysqli;
use mysqli_stmt;

/**
 * 
 * The MySQL Query class is used for running mysql querries
 * 
 * @author    MadByAd <adityaaw84@gmail.com>
 * @license   MIT License
 * @copyright Copyright (c) MadByAd 2024
 * 
 */

class MySQLQuery
{

    /**
     * Store the mysqli connection
     * 
     * @var mysqli
     */

    private ?mysqli $connection = null;

    /**
     * Store the prepared query
     * 
     * @var mysqli_stmt
     */

    private ?mysqli_stmt $preparedQuery = null;

    /**
     * Construct a new query
     * 
     * @param string      $query            The query
     * @param mysqli|MySQL|null $connection The connection, if null then it will use
     *                                      the default connection on the MySQL class
     * 
     * @throws MySQLNoConnectionException If no connection is supplied
     */

    public function __construct(string $query, mysqli|MySQL|null $connection = null)
    {
        
        if($connection == null) {
            $this->connection = MySQL::getDefaultConnection();
        } elseif($connection instanceof MySQL) {
            $this->connection = $connection->getConnection();
        } else {
            $this->connection = $connection;
        }

        if($this->connection == null) {
            throw new MySQLNoConnectionException("Error: no mysql connection is supplied");
        }

        $this->preparedQuery = mysqli_prepare($this->connection, $query);

    }

    /**
     * Bind values to the query (using this method will prevent sql injection)
     * 
     * @param array ...$values The value
     * 
     * @return void
     * 
     * @throws ArgumentCountError if the number of binded values does not match
     *                            the number of parameters in the prepared query
     * @throws MySQLQueryFailToBindException if somehow failed to bind the values
     *                                       to the prepared query
     */

    public function bind(string|array|int|float ...$values)
    {

        $types = "";
        $valueToBind = [];

        foreach($values as $value) {

            if(is_string($value) || is_array($value)) {
                
                if(is_array($value)) {
                    $value = json_encode($value);
                }

                $types .= "s";
                $valueToBind[] = $value;
                continue;

            }
            
            if(is_int($value)) {
                $types .= "i";
                $valueToBind[] = $value;
                continue;
            }
            
            if(is_double($value) || is_float($value)) {
                $types .= "d";
                $valueToBind[] = $value;
                continue;
            }

        }

        if(!mysqli_stmt_bind_param($this->preparedQuery, $types, ...$valueToBind)) {
            $bindCount = count($values);
            throw new MySQLQueryFailToBindException("Failed to bind ({$bindCount}) values to the query");
        }

    }

    /**
     * Execute the query
     * 
     * @return void
     * 
     * @throws MySQLQueryFailToExecuteException if failed to execute the query
     */

    public function execute()
    {
        if(!mysqli_stmt_execute($this->preparedQuery)) {
            throw new MySQLQueryFailToExecuteException("Failed to execute the query");
        }
    }

    /**
     * Return the result of the query executed
     * 
     * @return array
     */

    public function result()
    {
        
        $result = mysqli_stmt_get_result($this->preparedQuery);
        $rows = [];

        while($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;

    }

}
