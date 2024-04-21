<?php

use MadByAd\MPLMySQL\MySQLQuery;
use PHPUnit\Framework\TestCase;

final class CreateTableTest extends TestCase
{

    public function testCreatingTableForPerformingTests()
    {

        $query = new MySQLQuery("CREATE TABLE test_user (
            name VARCHAR(255) PRIMARY KEY,
            password VARCHAR(255),
            description TEXT,
            number int
        )");
        
        $query->execute();

        $query = new MySQLQuery("CREATE TABLE test_post (
            name VARCHAR(255) PRIMARY KEY,
            text TEXT,
            message TEXT DEFAULT 'Lorem Ipsum Dolor Sit Amet'
        )");

        $query->execute();

        $this->assertSame(true, true);

    }

}
