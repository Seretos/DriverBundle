<?php
use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\mysqli\MysqliConnection;
use database\DriverBundle\tests\connection\AbstractFunctionalConnectionTest;

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

        $this->connection = $this->getFactory()
                                 ->convertMysqli($mysqli);
        $this->assertInstanceOf(MysqliConnection::class, $this->connection);
    }

    /**
     * @test
     */
    public function secondConnection () {
        $connection2 = $this->getFactory()
                            ->createMysqliConnection(self::CONFIG['host'],
                                                     self::CONFIG['user'],
                                                     self::CONFIG['password'],
                                                     self::CONFIG['database']);
        $this->assertNotSame($this->connection, $connection2);
        $this->assertInstanceOf(MysqliConnection::class, $connection2);
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