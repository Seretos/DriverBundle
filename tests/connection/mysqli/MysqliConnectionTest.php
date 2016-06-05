<?php
use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\mysqli\MysqliConnection;
use database\DriverBundle\connection\mysqli\MysqliStatement;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 23.04.2016
 * Time: 23:23
 */
class MysqliConnectionTest extends PHPUnit_Framework_TestCase {
    /**
     * @var ReflectionClass
     */
    private $connectionReflection;
    /**
     * @var MysqliConnection
     */
    private $connection;
    /**
     * @var mysqli|PHPUnit_Framework_MockObject_MockObject
     */
    private $mysqli;

    public function setUp () {
        $this->connectionReflection = new ReflectionClass(MysqliConnection::class);
        $this->mysqli = $this->getMockBuilder(mysqli::class)
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->connection = new MysqliConnection($this->mysqli);
    }

    /**
     * @test
     */
    public function construct () {
        $connectionProperty = $this->connectionReflection->getProperty('_connection');
        $inTransactionProperty = $this->connectionReflection->getProperty('inTransaction');

        $connectionProperty->setAccessible(true);
        $inTransactionProperty->setAccessible(true);

        $this->assertSame($this->mysqli, $connectionProperty->getValue($this->connection));
        $this->assertSame(false, $inTransactionProperty->getValue($this->connection));
    }

    /**
     * @test
     */
    public function prepare () {
        $this->mysqli->expects($this->once())
                     ->method('prepare')
                     ->with('SELECT * FROM parameter')
                     ->will($this->returnValue(null));

        $statement = $this->connection->prepare('SELECT * FROM parameter');

        $this->assertInstanceOf(MysqliStatement::class, $statement);
    }

    /**
     * @test
     */
    public function beginTransaction () {
        $this->mysqli->expects($this->once())
                     ->method('begin_transaction')
                     ->will($this->returnValue(true));

        $this->assertSame(false, $this->connection->inTransaction());
        $this->assertSame(true, $this->connection->beginTransaction());
        $this->assertSame(true, $this->connection->inTransaction());
    }

    /**
     * @test
     */
    public function commit () {
        $inTransactionProperty = $this->connectionReflection->getProperty('inTransaction');
        $inTransactionProperty->setAccessible(true);
        $inTransactionProperty->setValue($this->connection, true);

        $this->mysqli->expects($this->once())
                     ->method('commit')
                     ->will($this->returnValue(true));

        $this->assertSame(true, $this->connection->inTransaction());
        $this->assertSame(true, $this->connection->commit());
        $this->assertSame(false, $this->connection->inTransaction());
    }

    /**
     * @test
     */
    public function commit_withError () {
        /* @var $mockConnection MysqliConnection|PHPUnit_Framework_MockObject_MockObject */
        $mockConnection = $this->getMockBuilder(MysqliConnection::class)
                               ->setConstructorArgs([
                                                        $this->mysqli,
                                                        ''
                                                    ])
                               ->setMethods(['getConnectionError', 'getConnectionErrorNumber'])
                               ->getMock();

        $this->mysqli->expects($this->once())
                     ->method('commit')
                     ->will($this->returnValue(false));

        $mockConnection->expects($this->once())
                       ->method('getConnectionError')
                       ->will($this->returnValue('test'));
        $mockConnection->expects($this->once())
                       ->method('getConnectionErrorNumber')
                       ->will($this->returnValue(4));

        $mockConnection->beginTransaction();
        $this->setExpectedExceptionRegExp(ConnectionException::class, '/test/', 4);
        $mockConnection->commit();
    }

    /**
     * @test
     */
    public function rollBack () {
        $inTransactionProperty = $this->connectionReflection->getProperty('inTransaction');
        $inTransactionProperty->setAccessible(true);
        $inTransactionProperty->setValue($this->connection, true);

        $this->mysqli->expects($this->once())
                     ->method('rollback')
                     ->will($this->returnValue(true));

        $this->assertSame(true, $this->connection->inTransaction());
        $this->assertSame(true, $this->connection->rollBack());
        $this->assertSame(false, $this->connection->inTransaction());
    }

    /**
     * @test
     */
    public function quote () {
        $this->assertSame("'test\\'1\\''", $this->connection->quote('test\'1\''));
    }
}