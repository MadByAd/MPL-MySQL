<?php

use MadByAd\MPLMySQL\MySQL;
use MadByAd\MPLMySQL\MySQLBuilder;
use PHPUnit\Framework\TestCase;

final class QueryBuilderTest extends TestCase
{

    /**
     * Test inserting and building a select query
     */

    public function testInsertAndBuildSelectQuery()
    {
        MySQL::setDefaultConnection("localhost", "root", "", "test", 3306);

        $query = new MySQLBuilder();

        $query->queryInsert("test_user", ["name", "password", "description", "number"], ["Adit", "12345", "Hello World!", 15]);
        $query->queryInsert("test_user", ["name", "password", "description", "number"], ["Budi", "54321", "Halo Dunia!", 51]);
        $query->queryInsert("test_user", ["name", "password", "description", "number"], ["JOKO", "09876", "PPPPPPPPP", 99]);

        $query->queryInsert("test_post", ["name", "text"], ["Adit", "Apple"]);
        $query->queryInsert("test_post", ["name", "text"], ["Budi", "Banana"]);
        $query->queryInsert("test_post", ["name", "text"], ["JOKO", "Grape"]);

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

        $this->assertSame($array, $query->querySelect("test_user")->execute());

        $array2 = [
            0 => [
                "name" => "Adit",
                "password" => "12345",
            ],
        ];

        $this->assertSame($array2, $query->querySelect("test_user", ["name", "password"])->condition("name = ?", "Adit")->execute());

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

        $this->assertSame($array3, $query->querySelect("test_user")->orderBy("number", false)->execute());

        $array4 = [
            0 => [
                "name" => "Adit",
                "password" => "12345",
                "description" => "Hello World!",
                "number" => 15,
                "text" => "Apple",
                "message" => "Lorem Ipsum Dolor Sit Amet",
            ],
            1 => [
                "name" => "Budi",
                "password" => "54321",
                "description" => "Halo Dunia!",
                "number" => 51,
                "text" => "Banana",
                "message" => "Lorem Ipsum Dolor Sit Amet",
            ],
            2 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "PPPPPPPPP",
                "number" => 99,
                "text" => "Grape",
                "message" => "Lorem Ipsum Dolor Sit Amet",
            ],
        ];

        $this->assertSame($array4, $query->querySelect("test_user")->join("test_post", [], "name", "name")->execute());

    }

    /**
     * Test building an update query
     */

    public function testBuildingUpdateQuery()
    {

        $query = new MySQLBuilder();

        $query->queryUpdate("test_user")->set("description = ?", "Lorem Ipsum Dolor Sit Amet")->condition("name = ?", "JOKO")->execute();

        $array = [
            0 => [
                "name" => "JOKO",
                "password" => "09876",
                "description" => "Lorem Ipsum Dolor Sit Amet",
                "number" => 99,
            ],
        ];

        $this->assertSame($array, $query->querySelect("test_user")->condition("name = ?", "JOKO")->execute());

    }

    /**
     * Test building a delete query
     */

    public function testBuildingDeleteQuery()
    {

        $query = new MySQLBuilder();

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
            ]
        ];

        $query->queryDelete("test_user")->condition("number = ?", 99)->execute();
        
        $this->assertSame($array, $query->querySelect("test_user")->execute());

        $query->queryDelete("test_user")->execute();
        $query->queryDelete("test_post")->execute();

        $this->assertSame([], $query->querySelect("test_user")->execute());
        $this->assertSame([], $query->querySelect("test_post")->execute());

    }

}
