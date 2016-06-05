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
    public function createPdoConnection ($host, $user, $password, $database, $port = 3306) {
        return new PdoConnection($host, $user, $password, $database, $port);
    }

    public function createMysqliConnection ($host, $user, $password, $database, $port = 3306) {
        $mysqli = new \mysqli($host, $user, $password, $database, $port);

        return new MysqliConnection($mysqli);
    }

    public function convertMysqli (\mysqli $connection) {
        return new MysqliConnection($connection);
    }
}