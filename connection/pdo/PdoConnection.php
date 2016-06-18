<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:09
 */

namespace database\DriverBundle\connection\pdo;


use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use PDO;
use PDOException;

class PdoConnection implements ConnectionInterface {
    const ERROR_ARRAY_INDEX = 1;

    private $pdo;
    private $connectionId;

    public function __construct (PDO $pdo) {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connectionId = (int) $this->pdo->query('SELECT CONNECTION_ID() AS id')
                                              ->fetch(PDO::FETCH_ASSOC)['id'];
    }

    /**
     * @param string $sql
     *
     * @return PdoStatement
     */
    public function prepare ($sql) {
        $statement = new PdoStatement($this->pdo->prepare($sql, []));

        return $statement;
    }

    /**
     * @param string $sql
     *
     * @return PdoStatement
     */
    public function query ($sql) {
        $statement = $this->prepare($sql);
        $statement->execute();

        return $statement;
    }

    /**
     * @param string $statement
     *
     * @return bool
     */
    public function exec ($statement) {
        $this->pdo->exec($statement);

        return true;
    }

    /**
     * @return bool
     * @throws ConnectionException
     */
    public function commit () {
        try {
            return $this->pdo->commit();
        } catch (PDOException $e) {
            throw new ConnectionException($e->getMessage(), $this->pdo->errorInfo()[self::ERROR_ARRAY_INDEX], $e);
        }
    }

    public function getConnectionId () {
        return $this->connectionId;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function quote ($string) {
        return $this->pdo->quote($string);
    }

    /**
     * @return int
     */
    public function lastInsertId () {
        return $this->pdo->lastInsertId();
    }

    /**
     * @return boolean
     */
    public function beginTransaction () {
        return $this->pdo->beginTransaction();
    }

    /**
     * @return boolean
     */
    public function rollBack () {
        return $this->pdo->rollBack();
    }

    /**
     * @return boolean
     */
    public function inTransaction () {
        return $this->pdo->inTransaction();
    }
}
