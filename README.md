DriverBundle
============
This bundle normalize the differences between mysqli and pdo.

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"LimetecBiotechnologies/database/DriverBundle" : "v0.2.*"
},
"repositories" : [
    ...
    ,{
        "type" : "git",
        "url" : "https://github.com/Seretos/DriverBundle"
    }
]
```
and execute the composer update command

Usage
=====
you can create a mysqli or an pdo connection, and use the [ConnectionInterface](connection/interfaces/ConnectionInterface.php) / [StatementInterface](connection/interfaces/StatementInterface.php) for both types

convert a mysqli connection
```php
$driverFactory = new DriverBundleFactory();
$mysqli = new mysqli($host,$user,$password,$database);

//convert mysqli to MysqliConnection
$this->connection = $driverBundleFactory->convertMysqli($mysqli);

//create a new MysqliConnection
$this->connection = $driverBundleFactory->createMysqliConnection('127.0.0.1','user','password','dbname',3306);
```
create a pdo connection
```php
$this->connection = $driverFactory->createPdoConnection($host,$user,$password,$database,3306);
```

create a statement:
```php
//returns a class of type StatementInterface
$statement = $this->connection->prepare('SELECT * FROM table WHERE column = :param1 AND column = :param2');
```

execute a statement:
```php
$statement->bindParam('param1',$param1);    //bind the variable reference
$statement->bindValue('param2','value');
$statement->execute();
```

save stream data:
```php
$fp = fopen('path/to/file.ext');
$statement->bindParam('param1',$fp,PDO::PARAM_LOB);
```

loop over results:
```php
// old fashioned way
while($row = $statement->fetch(PDO::FETCH_ASSOC)){
    ...
}

$statement->rewind();

// new way
$executedStatement->setFetchMode(PDO::FETCH_ASSOC);
foreach($executedStatement as $row){
    ...
}
```

Road map
========
the following features are not implemented but required for version 1.0

* debug logging for queries
* performance tests
* extended exception tests (how to test it?)
* extended blob tests
