# This is php database connection library

# First, create your .env file with these keys, you can use dotenv library to create environment variables:
```console
DRIVER=
DB_SERVERNAME=
DB_USER=
DB_PASSWORD=
DB_DBNAME=
```

# Example
```console
DRIVER=mysql
DB_SERVERNAME=localhost:3306
DB_USER=root
DB_PASSWORD=123abc
DB_DBNAME=my_db
```

**NOTE:**
It just supports two databases: MySQL and PostgreSQL.

# Usage
```php
<?php
$d = Database\DatabaseFactory::create();
$d->connect();
```
