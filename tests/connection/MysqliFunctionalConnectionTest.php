<?php
use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\tests\connection\AbstractFunctionalConnectionTest;
use database\DriverBundle\connection\mysqli\MysqliConnection;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:20
 */
class MysqliFunctionalConnectionTest extends AbstractFunctionalConnectionTest {
    protected function setUp () {
        parent::setUp();
        $mysqli = new mysqli(self::CONFIG['host'],
                             self::CONFIG['user'],
                             self::CONFIG['password'],
                             self::CONFIG['database']);

        $this->connection = new MysqliConnection($mysqli);
    }

    /**
     * @test
     */
    public function prepare_empty () {
        $this->setExpectedExceptionRegExp(ConnectionException::class);
        $statement = $this->connection->prepare('');
        unset($statement);
    }
}