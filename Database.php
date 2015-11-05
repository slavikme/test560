<?php

require_once "config.php";

/**
 * Custom database manager for the test.
 */
class Database
{
    /**
     * DB connection holder
     * @var PDO
     */
    static protected $connection = null;

    function __construct()
    {
        if ( !self::isConnected() ) {
            $this->connect(OGT_DB_HOST, OGT_DB_PORT, OGT_DB_NAME, OGT_DB_USERNAME, OGT_DB_PASSWORD);
            $this->createTables();
        }
    }

    /**
     * Connect to database and create the connection instance.
     * @param string $host Database's host name or IP address
     * @param string $port Database's port number
     * @param string $dbname The database name
     * @param string $user Username to connect to database
     * @param string $password The Password associated with the username
     * @return PDO
     * @throws Exception
     */
    private function connect($host, $port, $dbname, $user, $password)
    {
        try {
            self::$connection = new PDO("mysql;host=$host;port=$port;dbname=$dbname", $user, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$connection;
        } catch ( PDOException $e ) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * Checks whether an active connection to DB exists.
     * @return bool
     */
    static private function isConnected()
    {
        return !!self::$connection;
    }

    /**
     * Create all tables.
     * @return bool
     */
    private function createTables()
    {
        $query = file_get_contents("tables.sql");
        self::$connection->exec($query);
        return true;
    }

    /**
     * Retrieves all categories from the Categories table.
     * @param bool|false $name_to_id If this argument is set to true, the associative array will be
     * return when it's key is the category name (lowercase) and the value is category ID. When false,
     * it will be opposite.
     * @return array
     */
    public function getAllCategories($name_to_id = false)
    {
        $list = array();
        $query = "SELECT * FROM categories";
        foreach ( self::$connection->query($query) as $row )
        {
            if ( $name_to_id ) {
                $list[$row["name"]] = $row["id"];
            } else {
                $list[$row["id"]] = $row["name"];
            }
        }
        return $list;
    }

    /**
     * Inserts a new category into the DB.
     * @param string $name
     * @return integer The last inserted category ID. If the category already exists, will return its ID.
     */
    public function addCategory($name)
    {
        $name = trim(strtolower($name));
        if ( empty($name) ) {
            return false;
        }

        // Check if this category already exists
        $query = "SELECT * FROM categories WHERE `name`=?";
        $result = self::$connection->query($query);
        if ( count($result) ) {
            return $result[0]["id"];
        }

        // If not, create one
        $query = "INSERT INTO categories SET `name`=?";
        self::$connection
            ->prepare($query)
            ->execute(array($name));

        return self::$connection->lastInsertId();
    }

    /**
     * Inserts a new transaction into the DB.
     * @param string $category_name
     * @param string|int|float $price
     * @param string $datetime
     * @return integer The last inserted transaction ID.
     */
    public function addTransaction($category_name, $price, $datetime)
    {
        if ( ($cid = $this->addCategory($category_name)) === false
            || !is_numeric($price)
            || ($datetime = strtotime($datetime)) === false ) {
            return false;
        }

        $query = "INSERT INTO transactions SET `category_id`=?, `price`=?, `timestamp`=?";
        self::$connection
            ->prepare($query)
            ->execute(array($cid, $price, $datetime));

        return self::$connection->lastInsertId();
    }
}