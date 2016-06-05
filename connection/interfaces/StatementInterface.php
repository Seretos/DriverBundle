<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 04:08
 */

namespace database\DriverBundle\connection\interfaces;


interface StatementInterface extends \Iterator {
    /**
     * @param null|array $parameters
     *
     * @return boolean
     */
    public function execute ($parameters = null);

    /**
     * @return int
     */
    public function columnCount ();

    /**
     * @return int
     */
    public function rowCount ();

    /**
     * @return boolean
     */
    public function free ();

    /**
     * @param      $key
     * @param      $variable
     * @param null $type
     *
     * @return boolean
     */
    public function bindParam ($key, &$variable, $type = null);

    /**
     * @param      $key
     * @param      $value
     * @param null $type
     *
     * @return boolean
     */
    public function bindValue ($key, $value, $type = null);

    /**
     * @param null $type
     *
     * @return mixed
     */
    public function fetch ($type = null);

    /**
     * @param null $type
     *
     * @return mixed
     */
    public function fetchAll ($type = null);

    /**
     * @param $type
     *
     * @return boolean
     */
    public function setFetchMode ($type);
}