<?php

use MadByAd\MPLMySQL\Exceptions\MySQLQueryFailToExecuteException;
use MadByAd\MPLMySQL\MySQL;
use MadByAd\MPLMySQL\MySQLQuery;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{

    /**
     * Test binding incorrect amount of values will fail as expected
     */

    public function testFailedToBindToQueryAsExpected()
    {

        $this->expectException(ArgumentCountError::class);
        
        MySQL::setDefaultConnection("localhost", "root", "", "test", 3306);

        $query = new MySQLQuery("INSERT INTO test_user (name, password, description, number) VALUES (?,?,?,?)");
        $query->bind("Adit", "12345");
        $query->execute();

    }

    /**
     * Test inserting values into the table and grabbing it
     */

    public function testExecutingInsertAndSelectQuery()
    {

        $query = new MySQLQuery("INSERT INTO test_user (name, password, description, number) VALUES (?,?,?,?)");
        $query->bind("Adit", "12345", "Hello World!", 15);
        $query->execute();

        $query = new MySQLQuery("INSERT INTO test_user (name, password, description, number) VALUES (?,?,?,?)");
        $query->bind("Budi", "54321", "Halo Dunia!", 51);
        $query->execute();

        $query = new MySQLQuery("INSERT INTO test_user (name, password, description, number) VALUES (?,?,?,?)");
        $query->bind("JOKO", "09876", "PPPPPPPPP", 99);
        $query->execute();

        $query = new MySQLQuery("SELECT * FROM test_user");
        $query->execute();
        $result = $query->result();

        $array = [
            0 => [
                "name" => "Adit",
                "password" => "12345",
                "description" => "Hello World!",
                "number" => 15,
            ],
            1 => [
                "name" => "Budi",
                "password" => "54321",
                "description" => "Halo Dunia!",
                "number" => 51,
            ],
            2 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "PPPPPPPPP",
                "number" => 99,
            ],
        ];

        $this->assertSame($array, $result);
        
    }

    /**
     * Test executing a query with a variable that is not unique will fail as expected (because the name is already used)
     */

    public function testExecutingQueryWillFailAsExpected()
    {

        $this->expectException(MySQLQueryFailToExecuteException::class);

        $query = new MySQLQuery("INSERT INTO test_user (name, password, description, number) VALUES (?,?,?,?)");
        $query->bind("Adit", "12345", "Hello World!", 15);
        $query->execute();

    }

    /**
     * Test updating data
     */

    public function testExecutingUpdateQuery()
    {

        $query = new MySQLQuery("UPDATE test_user SET description = ? WHERE number = ?");
        $query->bind("Lorem Ipsum Dolor Sit Amet Consectecture Edipsing Elit", 99);
        $query->execute();

        $query = new MySQLQuery("SELECT * FROM test_user");
        $query->execute();
        $result = $query->result();

        $array = [
            0 => [
                "name" => "Adit",
                "password" => "12345",
                "description" => "Hello World!",
                "number" => 15,
            ],
            1 => [
                "name" => "Budi",
                "password" => "54321",
                "description" => "Halo Dunia!",
                "number" => 51,
            ],
            2 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "Lorem Ipsum Dolor Sit Amet Consectecture Edipsing Elit",
                "number" => 99,
            ],
        ];

        $this->assertSame($array, $result);
        
    }

    /**
     * Test deletion
     */

    public function testExecutingDeleteQuery()
    {

        $query = new MySQLQuery("DELETE FROM test_user WHERE name = ?");
        $query->bind("Adit");
        $query->execute();

        $query = new MySQLQuery("SELECT * FROM test_user");
        $query->execute();

        $array = [
            0 => [
                "name" => "Budi",
                "password" => "54321",
                "description" => "Halo Dunia!",
                "number" => 51,
            ],
            1 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "Lorem Ipsum Dolor Sit Amet Consectecture Edipsing Elit",
                "number" => 99,
            ],
        ];

        $this->assertSame($array, $query->result());

        $query = new MySQLQuery("DELETE FROM test_user");
        $query->execute();

        $query = new MySQLQuery("SELECT * FROM test_user");
        $query->execute();

        $this->assertSame([], $query->result());
        
    }

}
