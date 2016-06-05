<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 14.05.2016
 * Time: 06:59
 */

namespace database\DriverBundle\connection\pdo;


use database\DriverBundle\connection\exception\ConnectionException;
use database\DriverBundle\connection\interfaces\StatementInterface;

class PdoStatement implements StatementInterface {
    /**
     * @var \PDOStatement
     */
    private $_statement;

    /**
     * @var array
     */
    private $cache;

    private $index;

    public function __construct (\PDOStatement $stmt) {
        $this->_statement = $stmt;
        $this->cache = [];
        $this->index = 0;
    }

    /**
     * @return mixed
     */
    public function current () {
        return $this->loadCurrent();
    }

    /**
     * @return mixed
     * @throws ConnectionException
     */
    public function next () {
        $this->index++;
        $current = $this->loadCurrent();
//        if ($current == false) {
//            throw new ConnectionException('couldn\'t fetch result');
//        }

        return $current;
    }

    /**
     * Return the key of the current element
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key () {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid () {
        if ($this->index >= 0 && $this->index < $this->_statement->rowCount()) {
            return true;
        }

        return false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind () {
        $this->index = 0;
    }


    public function free () {
        if ($this->_statement instanceof \PDOStatement) {
            $this->cache = [];
            $this->_statement->closeCursor();
        }

        return true;
    }

    /**
     * @param null|array $parameters
     *
     * @return bool
     * @throws ConnectionException
     */
    public function execute ($parameters = null) {
        try {
            return $this->_statement->execute($parameters);
        } catch (\PDOException $e) {
            throw new ConnectionException($e->getMessage(),
                                          $this->_statement->errorInfo()[PdoConnection::ERROR_ARRAY_INDEX],
                                          $e);
        }
    }

    /**
     * @param int $type
     *
     * @return array|mixed
     */
    public function fetch ($type = null) {
        $row = $this->_statement->fetch($type);
        $this->cache[] = $row;

        return $row;
    }

    /**
     * @return int
     */
    public function columnCount () {
        return $this->_statement->columnCount();
    }

    /**
     * @return int
     */
    public function rowCount () {
        return $this->_statement->rowCount();
    }

    /**
     * @param      $key
     * @param      $variable
     * @param null $type
     *
     * @return boolean
     */
    public function bindParam ($key, &$variable, $type = null) {
        return $this->_statement->bindParam($key, $variable, $type);
    }

    /**
     * @param      $key
     * @param      $value
     * @param null $type
     *
     * @return boolean
     */
    public function bindValue ($key, $value, $type = null) {
        return $this->_statement->bindValue($key, $value, $type);
    }

    /**
     * @param null $type
     *
     * @return mixed
     */
    public function fetchAll ($type = null) {
        $this->_statement->fetchAll($type);
    }

    /**
     * @param $type
     *
     * @return boolean
     */
    public function setFetchMode ($type) {
        $this->_statement->setFetchMode($type);
    }

    private function loadCurrent () {
        while (count($this->cache) <= $this->index) {
            $this->fetch();
        }

        return $this->cache[$this->index];
    }
}