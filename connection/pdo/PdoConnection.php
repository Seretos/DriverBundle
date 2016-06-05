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

class PdoConnection extends \PDO implements ConnectionInterface {
    const ERROR_ARRAY_INDEX = 1;

    private $connectionId;

    /**
     * PdoConnection constructor.
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @param $port
     */
    public function __construct ($host, $user, $password, $database, $port = 3306) {
        parent::__construct('mysql:host='.$host.';port='.$port.';dbname='.$database, $user, $password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connectionId = (int) $this->query('SELECT CONNECTION_ID() AS id')
                                         ->fetch(PDO::FETCH_ASSOC)['id'];
    }

    /**
     * @param string $sql
     *
     * @return PdoStatement
     */
    public function prepare ($sql, $option = null) {
        $statement = new PdoStatement(parent::prepare($sql, []));

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
        parent::exec($statement);

        return true;
    }

    /**
     * @return bool
     * @throws ConnectionException
     */
    public function commit () {
        try {
            return parent::commit();
        } catch (PDOException $e) {
            throw new ConnectionException($e->getMessage(), $this->errorInfo()[self::ERROR_ARRAY_INDEX], $e);
        }
    }

    public function getConnectionId () {
        return $this->connectionId;
    }
}
