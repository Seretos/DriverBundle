<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 15.05.2016
 * Time: 10:30
 */

namespace database\DriverBundle\tests;


use database\DriverBundle\connection\interfaces\ConnectionInterface;

abstract class AbstractFunctionalDatabaseTest extends \PHPUnit_Framework_TestCase {
    const CONFIG = ['host' => '127.0.0.1', 'user' => 'root', 'password' => '', 'database' => 'test'];

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var \PDO
     */
    protected $pdo;

    protected function setUp () {
        try {
            $this->pdo = new \PDO('mysql:host='.self::CONFIG['host'].';',
                                  self::CONFIG['user'],
                                  self::CONFIG['password']);
        } catch (\Exception $e) {
            $this->fail('please set a valid database connection for tests in this file!');
        }
        $this->pdo->exec('DROP SCHEMA IF EXISTS '.self::CONFIG['database']);
        $this->pdo->exec('CREATE SCHEMA '.self::CONFIG['database']);
        $this->pdo->exec('USE '.self::CONFIG['database']);
        $this->pdo->exec('CREATE TABLE example1( id INT AUTO_INCREMENT,info VARCHAR(255), PRIMARY KEY(id));');
        $this->pdo->exec('CREATE TABLE example2( id INT AUTO_INCREMENT,info BLOB, PRIMARY KEY(id));');

        for ($i = 0; $i < 50; $i++) {
            $this->pdo->exec('INSERT INTO example1(info) VALUES(\'test'.$i.'\')');
            $this->pdo->exec('INSERT INTO example2(info) VALUES(\'test'.$i.'\')');
        }
    }
}
