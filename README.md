
# MPL MySQL

The MPL (MadByAd PHP Library) MySQL is a PHP library used to provide an easy interface for communicating with a mysql database. This library include a query runner to run your own written query (require an extensive knowledge of MYSQL), a query builder for easily writing querries or CRUD (stands for CREATE, READ, UPDATE, DELETE) which allows you to run the 4 basic query of mysql which is `INSERT`, `SELECT`, `UPDATE`, `DELETE` (The query builder and CRUD require minimal knowledge of mysql query)

## Installation

to install the package go ahead and open composer then write the command

```
composer require madbyad/mpl-mysql
```

## MySQL Class

The `MySQL` class is class used for establishing a mysql connection

### Constructing The Class

To establish a mysql connection you can create a new `MySQL(string $hostname = null, string $username = null, string $password = null, string $database = null, int $port = null)` class. It takes 5 parameter, the first is the mysql hostname, the second is the mysql username, the third is the mysql user password, the fourth is the database name to connect to, and the fifth is the port number.

### Establish Default Connection

You can also establish a default connection, which mean everytime you use the `MySQLQuery`, `MySQLBuilder`, or the `MySQLCRUD` class. You do not need to establish a new connection, you can just use the default connection. To establish a default connection, you can use the method `MySQL::setDefaultConnection(string $hostname = null, string $username = null, string $password = null, string $database = null, int $port = null)`. It takes 5 parameter, the first is the mysql hostname, the second is the mysql username, the third is the mysql user password, the fourth is the database name to connect to, and the fifth is the port number.

### Getting The Connection

If you have an instance of the `MySQL` class, you can use the method `getConnection()` To get the mysql connection

Or if you have already establish a default connection, you can use the method `MySQL::getDefaultConnection()`

**Example**

```php
// establish a new connection
$mysql = new MySQL("localhost", "root", "", "my_database");

// return the mysql connection
$mysql->getConnection();

// establish a default connection
MySQL::setDefaultConnection("localhost", "root", "", "my_database");

// return the default connection
MySQL::getDefaultConnection();
```

## MySQLQuery Class

The `MySQLQuery` class allows you to create a query, binds a value to the query, and execute the query. It is easy and provide a lot of freedom but, to use it require an extensive knowledge about mysql

### Constructing The Class

When constructing a new `MySQLQuery(string $query, mysqli $connection = null)` class. It takes 1 parameter and another 1 optional, the first is the query and the second is the mysql connection, if no connection is supplied then it will use the default connection established by the `MySQL` class, if there is no default connection, then it will throw a `MySQLNoConnectionException`

### Binding Values

To bind a values into the query, you can use the `bind(...$values)` method

Why binding values though, not just insert it in the query. Well it is done like this to prevent any sql injection

### Execute The Query

Finally, to execute the query, you can use the `execute()` method

### Getting The Result

If the query is a `SELECT` query, you may want to get the result. To get the result you can use the `result()` method. This will return the selected rows in a form of an associative array

**Example 1**

```php
// prepare a new query
// no connection is supplied so it will use the default connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?");

// bind values into the query
$query->bind($name);

// execute the query
$query->execute();

// return the result since it is a SELECT query
$result = $query->result();
```

**Example 2**

```php
// Establish a mysql connection
$mysql = new MySQL("localhost", "root", "", "my_database");

// prepare a new query
// use the given connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?", $mysql);

// bind values into the query
$query->bind($name);

// execute the query
$query->execute();

// return the result since it is a SELECT query
$result = $query->result();
```

## MySQLCRUD Class

The `MySQLCRUD` class allows you to perform the 4 basic sql querries which is `CREATE` or `INSERT`, `READ` or `SELECT`, `UPDATE`, and `DELETE`. The `MySQLCRUD` class require minimal knowledge of mysql to use and no need to write any sql querries

### Creating Data

To `CREATE` or `INSERT` data you can use the `MySQLCRUD::create(string $table, array $columns, array $values, mysqli|MySQL|null $connection = null)` method. it takes up 3 parameter and 1 optional parameter, the first is the table on which the data will be created to, the second is the columns which determine which columns of table should it fill, the third is the values for the columns, and the final one is the connection if `null` then it will use the default connection

**Example**

```php
// create a new user
MySQLCRUD::create("user", ["name", "password"], [$name, $password]);
```

### Reading Data

To read data, you can use the `MySQLCRUD::read(string $table, array $columns = null, array $condition = null, array $values = null, array $readSettings = null, mysqli|MySQL|null $connection = null)` method. It takes up 6 parameters, the first is the table on which data will be readed from, the second is the columns which determine which column should be readed, the third is the condition, the fourth is the values which will be binded to the condition (values for the condition), the fifth is the read settings which is an associative array containing the settings (can be used to determine limit, offset, and ordering), and the final is the connection if `null` then it will use the default connection. This method will return the readed rows in a form of associative array

**Example 1**

```php
// read from the table user and read all column
MySQLCRUD::read("user");
```

**Example 2**

```php
// read from the table user
// and only read the name and description column
MySQLCRUD::read("user", ["name", "description"]);
```

**Example 3**

```php
// read from the table user
// but only read if the name and password match the given value
MySQLCRUD::read("user", [], ["name = ?", "password = ?"], [$name, $password]);
```

**Example 4**

```php
// read from the table user
// limit the readed rows by 10
// offset the starting rows to read by 10
MySQLCRUD::read("user", [], [], [], [
    "limit" => 10,
    "offset" => 10,
]);
```

**Example 5**

```php
// read from the table user
// order the return rows by name ascendingly (A to Z)
MySQLCRUD::read("user", [], [], [], [
    "orderBy" => "name",
    "orderType" => "ascending",
]);
```

**Example 6**

```php
// read from the table user
// order the return rows by name descendingly (Z to A)
MySQLCRUD::read("user", [], [], [], [
    "orderBy" => "name",
    "orderType" => "ascending",
]);
```
