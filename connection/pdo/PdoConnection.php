<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:09
 */

namespace database\DriverBundle\connection\pdo;


use database\DriverBundle\connection\interfaces\ConnectionInterface;
use PDO;

class PdoConnection extends \PDO implements ConnectionInterface {
    /**
     * PdoConnection constructor.
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     */
    public function __construct ($host, $user, $password, $database) {
        parent::__construct('mysql:host='.$host.';dbname='.$database, $user, $password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $sql
     *
     * @return PdoStatement
     */
    public function prepare ($sql) {
        $statement = new PdoStatement(parent::prepare($sql, []));

        return $statement;
    }

    /**
     * @param string $sql
     *
     * @return PdoStatement
     */
    public function query ($sql) {
        $statement = new PdoStatement(parent::query($sql));

        return $statement;
    }
}