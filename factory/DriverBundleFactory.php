<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 04.06.16
 * Time: 02:44
 */

namespace database\DriverBundle\factory;


use database\DriverBundle\connection\mysqli\MysqliConnection;
use database\DriverBundle\connection\pdo\PdoConnection;

class DriverBundleFactory {
    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int    $port
     *
     * @return PdoConnection
     */
    public function createPdoConnection ($host, $user, $password, $database, $port = 3306) {
        $pdo = new \PDO('mysql:host='.$host.';port='.$port.';dbname='.$database, $user, $password);

        return $this->convertPdo($pdo);
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param int    $port
     *
     * @return MysqliConnection
     */
    public function createMysqliConnection ($host, $user, $password, $database, $port = 3306) {
        $mysqli = new \mysqli($host, $user, $password, $database, $port);

        return $this->convertMysqli($mysqli);
    }

    /**
     * @param \mysqli $connection
     *
     * @return MysqliConnection
     */
    public function convertMysqli (\mysqli $connection) {
        return new MysqliConnection($connection);
    }

    /**
     * @param \PDO $connection
     *
     * @return PdoConnection
     */
    public function convertPdo (\PDO $connection) {
        return new PdoConnection($connection);
    }
}