<?php

use MadByAd\MPLMySQL\Exceptions\MySQLInvalidDBException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidHostException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidPortException;
use MadByAd\MPLMySQL\Exceptions\MySQLInvalidUserException;
use MadByAd\MPLMySQL\MySQL;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{

    /**
     * Test whether we can establish a mysql connection
     */

    public function testEstablishingConnection()
    {

        $mysql = new MySQL("localhost", "root", "", "test");

        $this->assertInstanceOf("mysqli", $mysql->getConnection());

    }

    /**
     * Test whether we can establish a default mysql connection
     */

    public function testEstablishingDefaultConnection()
    {

        MySQL::setDefaultConnection("localhost", "root", "", "test");

        $this->assertInstanceOf("mysqli", MySQL::getDefaultConnection());

        $mysql = new MySQL();

        $this->assertSame(MySQL::getDefaultConnection(), $mysql->getConnection());

    }

    /**
     * Test if we give an invalid hostname it will throw an exception
     */

    public function testIfHostnameIsInvalidWillThrowException()
    {
        $this->expectException(MySQLInvalidHostException::class);
        new MySQL("invalid localhost", "root", "", "test");
    }

    /**
     * Test if we give an invalid username and password it will throw an exception
     */

    public function testIfUsernameAndPasswordIsInvalidWillThrowException()
    {
        $this->expectException(MySQLInvalidUserException::class);
        new MySQL("localhost", "invalid username", "invalid password", "test");
    }

    /**
     * Test if we give an invalid database it will throw an exception
     */

    public function testIfDatabaseIsInvalidWillThrowException()
    {
        $this->expectException(MySQLInvalidDBException::class);
        new MySQL("localhost", "root", "", "thisDatabaseDoesNotExist");
    }

    /**
     * Test if we give an invalid port number it will throw an exception
     */

    public function testIfPortNumberIsInvalidWillThrowException()
    {
        $this->expectException(MySQLInvalidPortException::class);
        new MySQL("localhost", "root", "", "test", 666666);
    }

}
