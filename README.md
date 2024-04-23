
# MPL MySQL

The MPL (MadByAd PHP Library) MySQL is a PHP library used to provide an easy interface for communicating with a mysql database. This library include a query runner to run your own written query (require an extensive knowledge of MYSQL), a query builder for easily writing querries or CRUD (stands for CREATE, READ, UPDATE, DELETE) which allows you to run the 4 basic query of mysql which is `INSERT`, `SELECT`, `UPDATE`, `DELETE` (The query builder and CRUD require minimal knowledge of mysql query)

- [MPL MySQL](#mpl-mysql)
  - [Installation](#installation)
  - [MySQL Class](#mysql-class)
    - [Constructing The Class](#constructing-the-class)
    - [Establish Default Connection](#establish-default-connection)
    - [Getting The Connection](#getting-the-connection)
  - [MySQLQuery Class](#mysqlquery-class)
    - [Constructing The Class](#constructing-the-class-1)
    - [Binding Values](#binding-values)
    - [Execute The Query](#execute-the-query)
    - [Getting The Result](#getting-the-result)
  - [MySQLCRUD Class](#mysqlcrud-class)
    - [Creating Data](#creating-data)
    - [Reading Data](#reading-data)
    - [Updating Data](#updating-data)
    - [Deleting Data](#deleting-data)
  - [MySQLBuilder Class](#mysqlbuilder-class)
    - [Constructing The Class](#constructing-the-class-2)
    - [Inserting Values](#inserting-values)
    - [Selecting Values](#selecting-values)
      - [execute()](#execute)
      - [join()](#join)
      - [condition()](#condition)
      - [limit()](#limit)
      - [offset()](#offset)
      - [orderBy()](#orderby)
    - [Updating Values](#updating-values)
      - [set()](#set)
      - [execute()](#execute-1)
      - [condition()](#condition-1)
    - [Deleting Values](#deleting-values)
      - [execute()](#execute-2)
      - [condition()](#condition-2)

## Installation

to install the package go ahead and open composer then write the command

```
composer require madbyad/mpl-mysql
```

## MySQL Class

The `MySQL` class is a class used for establishing a mysql connection

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

**Example 1**

```php
// prepare a new query
// no connection is supplied so it will use the default connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?");
```

**Example 2**

```php
// Establish a mysql connection
$mysql = new MySQL("localhost", "root", "", "my_database");

// prepare a new query
// use the given connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?", $mysql);
```

### Binding Values

To bind a values into the query, you can use the `bind(...$values)` method

Why binding values though, not just insert it in the query. Well it is done like this to prevent any sql injection

**Example**

```php
// prepare a new query
// no connection is supplied so it will use the default connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?");

// bind values into the query
$query->bind($name);
```

### Execute The Query

Finally, to execute the query, you can use the `execute()` method

**Example**

```php
// prepare a new query
// no connection is supplied so it will use the default connection
$query = new MySQLQuery("SELECT * FROM user WHERE name LIKE ?");

// bind values into the query
$query->bind($name);

// execute the query
$query->execute();
```

### Getting The Result

If the query is a `SELECT` query, you may want to get the result. To get the result you can use the `result()` method. This will return the selected rows in a form of an associative array

**Example**

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

### Updating Data

To update data you can use the method `MySQLCRUD::update(string $table, array $columns, array $columnValues, array $condition = null, array $conditionValues = null, mysqli|MySQL|null $connection = null)`. Tt takes up 6 parameters, the first is the table to have its data update, the second is the list of column to update, the third is the new values for those columns, the fourth is the condition requirement, the fifth is the values for the condition, the sixth is the mysql connection if `null` then it will use the default connection.

**Example**

```php
// Update the user description who has the given name and id
MySQLCRUD::update(
    "user",
    ["description = ?"],
    [$description],
    ["name = ?", "id = ?"],
    [$name, $id]
);
```

### Deleting Data

To delete data you can use the method `MySQLCRUD::delete(string $table, array $condition = null, array $values = null, mysqli|MySQL|null $connection = null)`. It takes up 4 parameters, the first is the table to have its data deleted, the second is the condition for the data that will be deleted, the third is the values for the condition, the fourth is the mysql connection if `null` then it will use the default connection

**Example 1**

```php
// delete a user who has the given name and id
MySQLCRUD::delete("user", ["name = ?", "id = ?"], [$name, $id]);
```

**Example 2**

```php
// WARNING
// if no condition is supplied this will delete all data
MySQLCRUD::delete("user");
```

## MySQLBuilder Class

The `MySQLBuilder` class allows you to build your query instead of writing mysql query. To use it require minimal knowledge of mysql query

### Constructing The Class

When constructing a new `MySQLBuilder(mysqli $connection = null)` class. It takes 1 optional parameter, which is the mysql connection, if no connection is supplied then it will use the default connection established by the `MySQL` class, if there is no default connection, then it will throw a `MySQLNoConnectionException`

### Inserting Values

To insert a value to a table it is just like the `MySQLCRUD` class, instead it use the method `queryInsert(string $table, array $columns, array $values)` and takes 3 parameters, the first is the table, the second is the columns to fill, the third is the values for the columns

**Example**

```php
// construct the class
$query = new MySQLBuilder;

// insert a new user
$query->queryInsert("user", ["name", "password"], [$name, $password]);
```

### Selecting Values

To construct a select query, you can use the method `querySelect(string $table, array $columns = [])`. It takes 2 parameter, the first is the table, the second is the column  to select

***Example 1***

```php
// construct the class
$query = new MySQLBuilder;

// select from the table user and grab all column
$query->querySelect("user", []);
```

***Example 2***

```php
// construct the class
$query = new MySQLBuilder;

// select from the table user and grab the name and description column
$query->querySelect("user", ["name", "description"]);
```

#### execute()

The `execute()` method will execute the constructed query

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table user and grab the name and description column
// * then executed the query
$query->querySelect("user", ["name", "description"])
    ->execute();
```

#### join()

The `join(string $tableToJoin, array $columns = [], string $columnFromTable = "", string $columnFromJoin = "")` method is used for joining a table. It takes 4 parameters, the first is the table to join, the second is the columns from the joined table to be selected, the third is the column from the original table which will be used for comparing with the column on the joined table join, the fourth is the column from the joined table which will be used for comparing with the column on the original table join

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * join the table user
//   based on the column owner on the post table
//   and the column name on the user table
// * then executed the query
$query->querySelect("post", [])
    ->join("user", [], "owner", "name")
    ->execute();
```

#### condition()

The `condition(array|string $conditions, array|string|int|float $values)` method is used for adding condition to the query. it takes 2 parameter, the first is the condition or the list of conditions, the second is the value / values for the condition

***Example 1***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * only select if the name and id match the given name and id
// * then executed the query
$query->querySelect("user", [])
    ->condition(["name = ?", "id = ?"], [$name, $id])
    ->execute();
```

***Example 2***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * only select if the name match the name A or name B or Name C
// * then executed the query
$query->querySelect("user", [])
    ->condition(["name = ?"], [$nameA])
    ->condition(["name = ?"], [$nameB])
    ->condition(["name = ?"], [$nameC])
    ->execute();
```

***Example 3***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * only select if the name match the given name or the given id
// * then executed the query
$query->querySelect("user", [])
    ->condition(["name = ?"], [$name])
    ->condition(["id = ?"], [$id])
    ->execute();
```

#### limit()

the `limit(int $amount)` method is used for setting the selection limit

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * only select if the name match the given name
// * limit the selection by 10
// * then executed the query
$query->querySelect("user", [])
    ->condition(["name = ?"], [$name])
    ->limit(10);
    ->execute();
```

#### offset()

the `offset(int $amount)` method is used for setting the selection offset

***Example 1***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * only select if the name match the given name
// * offset the selection by 10
// * then executed the query
$query->querySelect("user", [])
    ->condition(["name = ?"], [$name])
    ->offset(10);
    ->execute();
```

***Example 2***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * limit the selection by 10
// * offset the selection by 10
// * then executed the query
$query->querySelect("user", [])
    ->limit(10);
    ->offset(10);
    ->execute();
```

#### orderBy()

the `orderBy(string $column, bool $ascending = true)` method is used for ordering the selection by a certain column. It takes 2 parameters, the first is the column for the selection to be ordered, and the second is whether should the order be ascending if `false` then it will be descending

***Example 1***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * order the selection by name column ascendingly A to Z
// * then executed the query
$query->querySelect("user", [])
    ->orderBy("name")
    ->execute();
```

***Example 2***

```php
// construct the class
$query = new MySQLBuilder;

// * select from the table post
// * order the selection by name column descendingly Z to A
// * then executed the query
$query->querySelect("user", [])
    ->orderBy("name", false)
    ->execute();
```

### Updating Values

To construct an update query, you can use the method `queryUpdate(string $table)`, it takes only 1 parameter and that is the table

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * update the user table
$query->queryUpdate("user");
```

#### set()

the `set(string $column, array|string|int|float $value)` method is used for setting a new values for the given column. It takes 2 parameter, the first is the column to be updated, the second is the new value

***Example 1***

```php
// construct the class
$query = new MySQLBuilder;

// * update the user table
// * update the column name to be the given new name
// * update the column picture to be the given new picture
$query->queryUpdate("user")
    ->set("name", $name);
    ->set("picture", $picture);
```

#### execute()

The `execute()` method will execute the constructed query

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * update the user table
// * update the column name to be the given new name
// * update the column picture to be the given new picture
// * then executed the query
$query->querySelect("user")
    ->set("name", $name);
    ->set("picture", $picture);
    ->execute();
```

#### condition()

The `condition(array|string $conditions, array|string|int|float $values)` method can also be used for adding condition to the query. it takes 2 parameter, the first is the condition or the list of conditions, the second is the value / values for the condition

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * update the user table
// * update the column name to be the given new name
// * update the column picture to be the given new picture
// * update only for the user who has the given id
// * then executed the query
$query->querySelect("user")
    ->set("name", $name);
    ->set("picture", $picture);
    ->condition(["id = ?"], $id)
    ->execute();
```

### Deleting Values

To construct a delete query, you can use the method `delete(string $table)`. It takes 1 parameter

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * delete data from the user table
$query->queryDelete("user");
```

#### execute()

The `execute()` method will execute the constructed query

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * delete data from the user table
// * then executed the query
$query->queryDelete("user")
    ->execute();
```

#### condition()

The `condition(array|string $conditions, array|string|int|float $values)` method can also be used for adding condition to the query. it takes 2 parameter, the first is the condition or the list of conditions, the second is the value / values for the condition

***Example***

```php
// construct the class
$query = new MySQLBuilder;

// * delete data from the user table
// * only delete data that has the same id as the given id
// * then executed the query
$query->querySelect("user")
    ->condition(["id = ?"], $id)
    ->execute();
```
