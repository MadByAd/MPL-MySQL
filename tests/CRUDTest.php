<?php

use MadByAd\MPLMySQL\MySQL;
use MadByAd\MPLMySQL\MySQLCRUD;
use PHPUnit\Framework\TestCase;

final class CRUDTest extends TestCase
{

    /**
     * Creating and Reading data from / to the database
     */
    
    public function testCreateAndRead()
    {
        
        MySQL::setDefaultConnection("localhost", "root", "", "test", 3306);

        MySQLCRUD::create("test_user", ["name", "password", "description", "number"], ["Adit", "12345", "Hello World!", 15]);
        MySQLCRUD::create("test_user", ["name", "password", "description", "number"], ["Budi", "54321", "Halo Dunia!", 51]);
        MySQLCRUD::create("test_user", ["name", "password", "description", "number"], ["JOKO", "09876", "PPPPPPPPP", 99]);

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

        $this->assertSame($array, MySQLCRUD::read("test_user"));

        $array2 = [
            0 => [
                "name" => "Adit",
                "password" => "12345",
            ],
        ];

        $this->assertSame($array2, MySQLCRUD::read("test_user", ["name", "password"], ["name = ?"], ["Adit"]));

        $array3 = [
            0 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "PPPPPPPPP",
                "number" => 99,
            ],
            1 => [
                "name" => "Budi",
                "password" => "54321",
                "description" => "Halo Dunia!",
                "number" => 51,
            ],
            2 => [
                "name" => "Adit",
                "password" => "12345",
                "description" => "Hello World!",
                "number" => 15,
            ],
        ];

        $this->assertSame($array3, MySQLCRUD::read("test_user", [], [], [], [
            "orderBy" => "number",
            "orderType" => "descending",
        ]));

    }

    /**
     * Test updating data on the database
     */

    public function testUpdateData()
    {

        $number = 99;

        $newDescription = "Lorem Ipsum Dolor Sit Amet Consectecture Edipsing Elit";

        MySQLCRUD::update("test_user", ["description = ?"], [$newDescription], ["number = ?"], [$number]);

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
                "description" => $newDescription,
                "number" => 99,
            ],
        ];

        $this->assertSame($array, MySQLCRUD::read("test_user"));

    }

    /**
     * Test deleting data
     */

    public function testDeleteData()
    {

        MySQLCRUD::delete("test_user", ["name = ?"], ["JOKO"]);

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
        ];

        $this->assertSame($array, MySQLCRUD::read("test_user"));

        MySQLCRUD::delete("test_user");

        $this->assertSame([], MySQLCRUD::read("test_user"));

    }

}
