<?php

use MadByAd\MPLMySQL\MySQLQuery;
use PHPUnit\Framework\TestCase;

final class DeleteTableTest extends TestCase
{

    public function testDeleteTableBecauseTheTestIsOver()
    {
        
        $query = new MySQLQuery("DROP TABLE test_user");
        $query->execute();

        $query = new MySQLQuery("DROP TABLE test_post");
        $query->execute();

        $this->assertSame(true, true);

    }

}
