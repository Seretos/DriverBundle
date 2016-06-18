<?php
namespace database\DriverBundle\tests\connection\pdo;

use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\pdo\PdoConnection;
use database\DriverBundle\connection\pdo\PdoStatement;
use PDO;
use PDOException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 18.06.2016
 * Time: 21:24
 */
class PdoConnectionTest extends PHPUnit_Framework_TestCase {
    /**
     * @var PDO|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPdo;

    /**
     * @var PdoConnection
     */
    private $connection;

    protected function setUp () {
        $this->mockPdo = $this->getMockBuilder(PDO::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->mockPdo->expects($this->at(0))
                      ->method('setAttribute')
                      ->with(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $mockResult = $this->getMockBuilder(\PDOStatement::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $mockResult->expects($this->once())
                   ->method('fetch')
                   ->with(PDO::FETCH_ASSOC)
                   ->will($this->returnValue(['id' => '3']));

        $this->mockPdo->expects($this->at(1))
                      ->method('query')
                      ->with('SELECT CONNECTION_ID() AS id')
                      ->will($this->returnValue($mockResult));

        $this->connection = new PdoConnection($this->mockPdo);
        $this->assertSame(3, $this->connection->getConnectionId());
    }

    /**
     * @test
     */
    public function prepare () {
        $mockResult = $this->getMockBuilder(\PDOStatement::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->mockPdo->expects($this->once())
                      ->method('prepare')
                      ->with('test', [])
                      ->will($this->returnValue($mockResult));

        $stmt = $this->connection->prepare('test');

        $this->assertInstanceOf(PdoStatement::class, $stmt);
    }

    /**
     * @test
     */
    public function query () {
        $mockResult = $this->getMockBuilder(\PDOStatement::class)
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->mockPdo->expects($this->once())
                      ->method('prepare')
                      ->with('test', [])
                      ->will($this->returnValue($mockResult));

        $mockResult->expects($this->once())
                   ->method('execute');

        $stmt = $this->connection->query('test');

        $this->assertInstanceOf(PdoStatement::class, $stmt);
    }

    /**
     * @test
     */
    public function execMethod () {
        $this->mockPdo->expects($this->once())
                      ->method('exec')
                      ->with('test');

        $this->assertSame(true, $this->connection->exec('test'));
    }

    /**
     * @test
     */
    public function commit () {
        $this->mockPdo->expects($this->once())
                      ->method('commit')
                      ->will($this->returnValue('success'));

        $this->assertSame('success', $this->connection->commit());
    }

    /**
     * @test
     */
    public function commit_withException () {
        $this->mockPdo->expects($this->once())
                      ->method('errorInfo')
                      ->will($this->returnValue([PdoConnection::ERROR_ARRAY_INDEX => 4]));

        $this->mockPdo->expects($this->once())
                      ->method('commit')
                      ->willThrowException(new PDOException('success'));

        $this->setExpectedExceptionRegExp(ConnectionException::class, '/success/', 4);
        $this->connection->commit();
    }

    /**
     * @test
     */
    public function quote () {
        $this->mockPdo->expects($this->once())
                      ->method('quote')
                      ->with('test')
                      ->will($this->returnValue('success'));

        $this->assertSame('success', $this->connection->quote('test'));
    }

    /**
     * @test
     */
    public function lastInsertId () {
        $this->mockPdo->expects($this->once())
                      ->method('lastInsertId')
                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->connection->lastInsertId());
    }

    /**
     * @test
     */
    public function beginTransaction () {
        $this->mockPdo->expects($this->once())
                      ->method('beginTransaction')
                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->connection->beginTransaction());
    }

    /**
     * @test
     */
    public function rollBack () {
        $this->mockPdo->expects($this->once())
                      ->method('rollBack')
                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->connection->rollBack());
    }

    /**
     * @test
     */
    public function inTransaction () {
        $this->mockPdo->expects($this->once())
                      ->method('inTransaction')
                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->connection->inTransaction());
    }
}