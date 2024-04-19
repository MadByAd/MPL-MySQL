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

use MadByAd\MPLMySQL\Exceptions\MySQLInvalidDBException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidHostException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidPortException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidUserException;
use mysqli;

/**
 * 
 * The MySQL class is used for storing mysql connection
 * 
 * @author    MadByAd <adityaaw84@gmail.com>
 * @license   MIT License
 * @copyright Copyright (c) MadByAd 2024
 * 
 */

class MySQL
{

    /**
     * The keyword for detecting warning because of invalid hostname
     * 
     * @var string
     */

    private const INVALID_HOST_KEYWORD = "php_network_getaddresses";

    /**
     * The keyword for detecting warning because of invalid port
     * 
     * @var string
     */
    
    private const INVALID_PORT_KEYWORD = "No connection could be made";

    /**
     * The keyword for detecting warning because of invalid username or password
     * 
     * @var string
     */
    
    private const INVALID_USER_KEYWORD = "Access denied";

    /**
     * The keyword for detecting warning because of the database not existing
     * 
     * @var string
     */
    
    private const INVALID_DB_KEYWORD = "Unknown database";

    /**
     * The default hostname
     * 
     * @var string
     */

    private static ?string $hostname = null;

    /**
     * The default username
     * 
     * @var string
     */
    
    private static ?string $username = null;

    /**
     * The default password
     * 
     * @var string
     */
    
    private static ?string $password = null;

    /**
     * The default database
     * 
     * @var string
     */

    private static ?string $database = null;

    /**
     * The default port
     * 
     * @var string
     */

    private static ?int $port = null;

    /**
     * The default mysqli connection
     * 
     * @var mysqli
     */

    private static $defaultConnection = null;

    /**
     * The mysqli connection
     * 
     * @var mysqli
     */

    private $connection = null;

    /**
     * Construct a new MySQL connection, if no parameter is given then it will use
     * the default connection only if the default connection is established
     * 
     * @param string $hostname The hostname
     * @param string $username The mysql username
     * @param string $password The mysql user password
     * @param string $database The database name to connect to
     * @param int    $port     The port
     * 
     * @return void
     */

    public function __construct(string $hostname = null, string $username = null, string $password = null, string $database = null, int $port = null)
    {

        if($hostname == null && $username == null && $password == null && $database == null && $port == null && self::$defaultConnection != null) {
            $this->connection = self::$defaultConnection;
            return;
        }

        if($hostname == null) {
            $hostname = self::$hostname;
        }

        if($username == null) {
            $username = self::$username;
        }

        if($password == null) {
            $password = self::$password;
        }

        if($database == null) {
            $database = self::$database;
        }

        if($port == null) {
            $port = self::$port;
        }

        ob_start();
        $this->connection = mysqli_connect($hostname, $username, $password, $database, $port);
        $warning = ob_get_clean();

        if(!empty($warning)) {
            if(str_contains($warning, self::INVALID_HOST_KEYWORD)) {
                throw new MySQLInvalidHostException("Error: invalid hostname or target machine may not active, unable to connect to database");
            }
            if(str_contains($warning, self::INVALID_PORT_KEYWORD)) {
                throw new MySQLInvalidPortException("Error: invalid port or target machine may not active, unable to connect to database");
            }
            if(str_contains($warning, self::INVALID_USER_KEYWORD)) {
                throw new MySQLInvalidUserException("Error: invalid username or password, cannot access database");
            }
            if(str_contains($warning, self::INVALID_DB_KEYWORD)) {
                throw new MySQLInvalidDBException("Error: cannot connect to the database, the database \"{$database}\" does not exist");
            }
        }

    }

    /**
     * Set the default mysql connection
     * 
     * @param string $hostname The hostname
     * @param string $username The mysql username
     * @param string $password The mysql user password
     * @param string $database The database name to connect to
     * @param int    $port     The port
     * 
     * @return void
     */

    public static function setDefaultConnection(string $hostname = null, string $username = null, string $password = null, string $database = null, int $port = null)
    {

        ob_start();
        self::$defaultConnection = mysqli_connect($hostname, $username, $password, $database, $port);
        $warning = ob_get_clean();

        if(!empty($warning)) {
            if(str_contains($warning, self::INVALID_HOST_KEYWORD)) {
                throw new MySQLInvalidHostException("Error: invalid hostname or target machine may not active, unable to connect to database");
            }
            if(str_contains($warning, self::INVALID_PORT_KEYWORD)) {
                throw new MySQLInvalidPortException("Error: invalid port or target machine may not active, unable to connect to database");
            }
            if(str_contains($warning, self::INVALID_USER_KEYWORD)) {
                throw new MySQLInvalidUserException("Error: invalid username or password, cannot access database");
            }
            if(str_contains($warning, self::INVALID_DB_KEYWORD)) {
                throw new MySQLInvalidDBException("Error: cannot connect to the database, the database \"{$database}\" does not exist");
            }
        }

        self::$hostname = $hostname;
        self::$username = $username;
        self::$password = $password;
        self::$database = $database;
        self::$port = $port;

    }

    /**
     * Get the mysql connection
     * 
     * @return mysqli The mysqli connection
     */

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the mysql default connection
     * 
     * @return mysqli|null The default mysqli connection or null if no default connection has been set
     */

    public static function getDefaultConnection()
    {
        return self::$defaultConnection;
    }

}
