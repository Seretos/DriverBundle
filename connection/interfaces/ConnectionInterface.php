<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:08
 */

namespace database\DriverBundle\connection\interfaces;


interface ConnectionInterface {
    /**
     * @param string $sql
     *
     * @return StatementInterface
     */
    public function prepare ($sql);

    /**
     * WARNING: mysqli and pdo return different results on select!
     * WARNING: multiple statements are not supported in mysqli
     *
     * @param $sql
     *
     * @return int
     */
    public function exec ($sql);

    /**
     * @param $sql
     *
     * @return StatementInterface
     */
    public function query ($sql);

    /**
     * @param string $string
     *
     * @return string
     */
    public function quote ($string);

    /**
     * @return int
     */
    public function lastInsertId ();

    /**
     * @return boolean
     */
    public function commit ();

    /**
     * @return boolean
     */
    public function beginTransaction ();

    /**
     * @return boolean
     */
    public function rollBack ();

    /**
     * @return boolean
     */
    public function inTransaction ();

    /**
     * @return int
     */
    public function getConnectionId ();
}