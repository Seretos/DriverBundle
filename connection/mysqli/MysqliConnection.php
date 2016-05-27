<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 18.04.2016
 * Time: 23:45
 */

namespace database\DriverBundle\connection\mysqli;

use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\interfaces\ConnectionInterface;

class MysqliConnection extends MysqliWrapper implements ConnectionInterface {
    private $inTransaction;

    /**
     * MysqliConnection constructor.
     *
     * @param \mysqli $connection
     */
    public function __construct (\mysqli $connection) {
        parent::__construct($connection);
        $this->inTransaction = false;
    }

    /**
     * @param string $sql
     *
     * @return MysqliStatement
     */
    public function prepare ($sql) {
        return new MysqliStatement($this->_connection, $sql);
    }

    /**
     * @param $sql
     *
     * @return int
     * @throws \database\DriverBundle\connection\exception\ConnectionException
     */
    public function exec ($sql) {
        $statement = new MysqliStatement($this->_connection, $sql);
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * @param $sql
     *
     * @return MysqliStatement
     * @throws \database\DriverBundle\connection\exception\ConnectionException
     */
    public function query ($sql) {
        $statement = new MysqliStatement($this->_connection, $sql);
        $statement->execute();

        return $statement;
    }

    /**
     * @return int|string
     */
    public function lastInsertId () {
        return mysqli_insert_id($this->_connection);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function quote ($string) {
        return '\''.str_replace('\'', "\\'", $string).'\'';
    }

    /**
     * @return bool
     * @throws ConnectionException
     */
    public function commit () {
        $this->inTransaction = false;

        if (!$this->_connection->commit()) {
            throw new ConnectionException($this->getConnectionError(), $this->getConnectionErrorNumber());
        }

        return true;
    }

    /**
     * @return bool
     */
    public function beginTransaction () {
        $this->inTransaction = true;

        return $this->_connection->begin_transaction();
    }

    /**
     * @return bool
     */
    public function rollBack () {
        $this->inTransaction = false;

        return $this->_connection->rollback();
    }

    /**
     * @return bool
     */
    public function inTransaction () {
        return $this->inTransaction;
    }
}