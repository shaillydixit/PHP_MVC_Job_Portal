<?php

class Database {
    public $conn;

    /**
     * Constructor for the database class
     * 
     * @param array $config Configuration details for the database connection.
     */
    public function __construct($config) {
        // Data Source Name (DSN) for MySQL
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";

        // PDO options for error handling
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Set error mode to exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Set fetch mode to associative array
        ];
        
        try {
            // Create a new PDO instance and set it to $this->conn
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
            // echo "Connected successfully";
        } catch (PDOException $e) {
            // Handle connection errors
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

      /**
   * Query the database
   * 
   * @param string $query
   * 
   * @return PDOStatement
   * @throws PDOException
   */
  public function query($query, $params = [])
  {
    try{
     $sth = $this->conn->prepare($query);

    //  bind name parameters
    foreach($params as $param => $value)
    {
        $sth->bindValue(':' . $param, $value);
    }
     $sth->execute();
     return $sth;
    }catch (PDOException $e)
    {
        throw new Exception("Query failed to execute: {$e->getMessage()}");
    }
  }
}