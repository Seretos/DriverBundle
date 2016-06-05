<?php
namespace database\DriverBundle\tests\connection;

use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\factory\DriverBundleFactory;
use database\DriverBundle\tests\AbstractFunctionalDatabaseTest;
use PDO;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:16
 */
abstract class AbstractFunctionalConnectionTest extends AbstractFunctionalDatabaseTest {
    /**
     * @var DriverBundleFactory
     */
    private $factory;

    protected function setUp () {
        parent::setUp();
        $this->factory = new DriverBundleFactory();
    }

    /**
     * @return DriverBundleFactory
     */
    protected function getFactory () {
        return $this->factory;
    }

    /**
     * @test
     */
    public function exec () {
        $this->assertSame(true, $this->connection->exec('INSERT INTO example1(info) VALUES(\'query_insert\')'));
        $this->assertSame(true, $this->connection->exec('INSERT INTO example1(info) VALUES(\'query_insert\')'));
        $this->assertSame(true,
                          $this->connection->exec('UPDATE example1 SET info = \'query_insert2\' WHERE info = \'query_insert\''));
        $this->assertSame(true,
                          $this->connection->exec('DELETE FROM example1 WHERE info = \'query_insert2\''));
    }

    /**
     * @test
     */
    public function prepare_withParameter () {
        $statement = $this->connection->prepare('SELECT * FROM example1 WHERE info LIKE :param1');
        $parameter = 'test1';
        $statement->bindParam('param1', $parameter);
        $this->assertSame(true, $statement->execute());
        $this->assertSame(1, $statement->rowCount());
        $this->assertEquals(2, $statement->current()['id']);

        $parameter = 'test%';

        $this->assertSame(true, $statement->execute());
        $this->assertSame(50, $statement->rowCount());
        unset($parameter);
    }

    /**
     * @test
     */
    public function prepare_withValue () {
        $statement = $this->connection->prepare('SELECT * FROM example1 WHERE info LIKE :param1');
        $statement->bindValue('param1', 'test1');
        $this->assertSame(true, $statement->execute());
        $this->assertSame(1, $statement->rowCount());
        $this->assertEquals(2, $statement->current()['id']);

        $statement->bindValue('param1', 'test%');

        $this->assertSame(true, $statement->execute());
        $this->assertSame(50, $statement->rowCount());
    }

    /**
     * @test
     */
    public function prepare_withExecute () {
        $statement = $this->connection->prepare('SELECT * FROM example1 WHERE info LIKE :param1');
        $parameter = 'test1';
        $statement->bindParam('param1', $parameter);
        $this->assertSame(true, $statement->execute(['param1' => 'test%']));
        $this->assertSame(50, $statement->rowCount());

        $this->assertSame(true, $statement->execute());
        $this->assertSame(50, $statement->rowCount());

        $this->assertSame(true, $statement->execute(['param1' => 'test1']));
        $this->assertSame(1, $statement->rowCount());
        $this->assertEquals(2, $statement->current()['id']);
    }

    /**
     * @test
     */
    public function query () {
        $statement = $this->connection->query('SELECT * FROM example1');
        $this->assertSame(50, $statement->rowCount());
        $this->assertSame(2, $statement->columnCount());
        $this->assertEquals(0, $this->connection->lastInsertId());
        for ($i = 0; $i < 50; $i++) {
            $this->assertEquals(['id' => $i + 1, '0' => $i + 1, 'info' => 'test'.$i, '1' => 'test'.$i],
                                $statement->current());
            $statement->next();
        }
        $statement->rewind();
        for ($i = 0; $i < 50; $i++) {
            $this->assertEquals(['id' => $i + 1, '0' => $i + 1, 'info' => 'test'.$i, '1' => 'test'.$i],
                                $statement->current());
            $this->assertSame($i, $statement->key());
            $this->assertSame(true, $statement->valid());
            $statement->next();
        }
        $this->assertSame(false, $statement->valid());
    }

    /**
     * @test
     */
    public function query_insert () {
        $statement = $this->connection->query('INSERT INTO example1(info) VALUES(\'query_insert\')');
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
        $this->assertEquals(51, $this->connection->lastInsertId());
        $statement = $this->connection->query('INSERT INTO example1(info) VALUES(\'query_insert\')');
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
        $this->assertEquals(52, $this->connection->lastInsertId());
        $statement = $this->connection->query('UPDATE example1 SET info = \'query_insert2\' WHERE info = \'query_insert\'');
        $this->assertSame(2, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
        $this->assertEquals(0, $this->connection->lastInsertId());

        $statement = $this->connection->query('DELETE FROM example1 WHERE info = \'query_insert2\'');
        $this->assertSame(2, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
    }

    /**
     * @test
     */
    public function fetch () {
        $statement = $this->connection->prepare('SELECT * FROM example1');
        $statement->execute();
        $index = 0;
        while ($row = $statement->fetch()) {
            $this->assertEquals(['0' => $index + 1, 'id' => $index + 1, '1' => 'test'.$index, 'info' => 'test'.$index],
                                $row);
            $index++;
        }

        $statement = $this->connection->prepare('SELECT * FROM example1');
        $statement->execute();
        $index = 0;
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $this->assertEquals(['id' => $index + 1, 'info' => 'test'.$index],
                                $row);
            $index++;
        }

        $statement = $this->connection->prepare('SELECT * FROM example1');
        $statement->execute();
        $index = 0;
        while ($row = $statement->fetch(\PDO::FETCH_NUM)) {
            $this->assertEquals(['0' => $index + 1, '1' => 'test'.$index],
                                $row);
            $index++;
        }

        $statement = $this->connection->prepare('SELECT * FROM example1');
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $index = 0;
        while ($row = $statement->fetch()) {
            $this->assertEquals(['id' => $index + 1, 'info' => 'test'.$index],
                                $row);
            $index++;
        }

        $statement = $this->connection->prepare('SELECT * FROM example1');
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        for ($index = 0; $index < count($result); $index++) {
            $this->assertEquals(['id' => $index + 1, 'info' => 'test'.$index],
                                $result[$index]);
        }
    }

    /**
     * @test
     */
    public function transaction () {
        $this->assertSame(false, $this->connection->inTransaction());

        $this->assertSame(true, $this->connection->beginTransaction());

        $statement = $this->connection->query('INSERT INTO example1(info) VALUES(\'query_insert\')');
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
        $this->assertNotEquals(0, $this->connection->lastInsertId());
        $id = $this->connection->lastInsertId();
        $statement = $this->connection->query('SELECT * FROM example1 WHERE id = '.$id);
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(2, $statement->columnCount());

        $statement = $this->pdo->query('SELECT * FROM example1 WHERE id = '.$id);
        $this->assertSame(0, $statement->rowCount());

        $this->assertSame(true, $this->connection->commit());
        $statement = $this->pdo->query('SELECT * FROM example1 WHERE id = '.$id);
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(2, $statement->columnCount());
    }

    /**
     * @test
     */
    public function transaction_rollback () {
        $this->assertSame(false, $this->connection->inTransaction());

        $this->assertSame(true, $this->connection->beginTransaction());

        $statement = $this->connection->query('INSERT INTO example1(info) VALUES(\'query_insert\')');
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(0, $statement->columnCount());
        $this->assertEquals(51, $this->connection->lastInsertId());

        $statement = $this->connection->query('SELECT * FROM example1 WHERE id = 51');
        $this->assertSame(1, $statement->rowCount());
        $this->assertSame(2, $statement->columnCount());

        $statement = $this->pdo->query('SELECT * FROM example1 WHERE id = 51');
        $this->assertSame(0, $statement->rowCount());

        $this->assertSame(true, $this->connection->rollBack());

        $statement = $this->connection->query('SELECT * FROM example1 WHERE id = 51');
        $this->assertSame(0, $statement->rowCount());
    }

    /**
     * test the loading of an stream param. its not possible to test with an unit test
     * @test
     */
    public function loadStreamParam () {
        $this->pdo->exec('DELETE FROM example4');
        $this->pdo->exec('DELETE FROM example3');
        $this->pdo->exec('DELETE FROM example2');
        $fp = fopen(__DIR__.'/../../phpunit.xml', 'rb');

        $statement = $this->connection->prepare('INSERT INTO example2(info) VALUES(:value)');
        $statement->bindValue('value', $fp, PDO::PARAM_LOB);
        $statement->execute();

        $statement = $this->connection->prepare('SELECT * FROM example2');
        $statement->execute();
        rewind($fp);
        $this->assertEquals(['id' => 51, 'info' => stream_get_contents($fp)], $statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @test
     */
    public function free () {
        $statement = $this->connection->prepare('SELECT * FROM example1 WHERE id < :param');
        $statement->bindValue('param', 3);
        $statement->execute();
        $this->assertEquals(['id' => 1, 0 => 1, 'info' => 'test0', 1 => 'test0'], $statement->current());
        $this->assertSame(true, $statement->free());
        $statement->next();
        $this->assertEquals(false, $statement->current());
    }

    /**
     * @test
     */
    public function serverError () {
        $this->connection->exec('LOCK TABLE example1 READ');
        try {
            $stmt = $this->connection->prepare('SELECT * FROM example1 AS e1');
            $stmt->execute();
        } catch (ConnectionException $e) {
            $this->assertContains('Table \'e1\' was not locked with LOCK TABLES', $e->getMessage());
            $this->assertSame(1100, $e->getCode());
        }
        $this->connection->exec('UNLOCK TABLES');
    }

    /**
     * @test
     */
    public function getConnectionId () {
        $statement = $this->connection->prepare('SELECT CONNECTION_ID() AS id');
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_NUM);
        $this->assertEquals($this->connection->getConnectionId(), $statement->current()[0]);
    }

    /**
     * @test
     */
    public function commit_withoutTransaction () {
        $this->setExpectedExceptionRegExp(ConnectionException::class, '/There is no active transaction/', 0);
        $this->connection->commit();
    }
}