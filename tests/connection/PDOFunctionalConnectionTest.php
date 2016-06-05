<?php
use database\DriverBundle\connection\pdo\PdoConnection;
use database\DriverBundle\tests\connection\AbstractFunctionalConnectionTest;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 06:33
 */
class PDOFunctionalConnectionTest extends AbstractFunctionalConnectionTest {
    protected function setUp () {
        parent::setUp();
        $this->connection = $this->getFactory()
                                 ->createPdoConnection(self::CONFIG['host'],
                                                       self::CONFIG['user'],
                                                       self::CONFIG['password'],
                                                       self::CONFIG['database']);
        $this->assertInstanceOf(PdoConnection::class, $this->connection);
    }
}