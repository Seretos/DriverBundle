DriverBundle
============
This bundle normalize the differences between mysqli and pdo.

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"Seretos/database/DriverBundle" : "0.9.0.*"
},
"repositories" : [
    ...
    ,{
        "type" : "vcs",
        "url" : "https://github.com/Seretos/DriverBundle"
    }
]
```
and execute the composer update command

Usage
=====
you can create a mysqli or an pdo connection, and use the [ConnectionInterface](connection/interfaces/ConnectionInterface.php) / [StatementInterface](connection/interfaces/StatementInterface.php) for both types

create a mysqli connection
```php
$mysqli = new mysqli($host,$user,$password,$database);

$this->connection = new MysqliConnection($mysqli);
```
create a pdo connection
```php
$this->connection = new PdoConnection($host,$user,$password,$database);
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

* normalized exception handling for timeout, deadlock, duplicate key (problem: how to test it?)
