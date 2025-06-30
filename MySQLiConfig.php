<?php
require_once "patterns/Singleton.php";

class MySQLiConfig extends Singleton implements DBConnectionInterface
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;
    private $isConnected = false;


    /**
     * @throws Exception
     */
    protected function __construct()
    {
        $this->loadConfig();
    }

    /**
     * @throws Exception
     */
    private function loadConfig()
    {
        if (!file_exists('database.ini')) {
            throw new Exception('Database configuration file not found');
        }

        $config = parse_ini_file('database.ini', true);

        if (!isset($config['database'])) {
            throw new Exception('Invalid database configuration');
        }

        $this->host = $config['database']['host'] ?? 'localhost';
        $this->username = $config['database']['username'] ?? '';
        $this->password = $config['database']['password'] ?? '';
        $this->database = $config['database']['database'] ?? '';
    }

    /**
     * @throws Exception
     */
    public function Connect(){
        if ($this->isConnected) {
            return; // Already connected
        }

        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);

        if (!$this->connection) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }

        mysqli_set_charset($this->connection, 'utf8mb4');
        $this->isConnected = true;
    }

    /**
     * @throws Exception
     */
    public function Execute($Q){
        $this->ensureConnected();

        $result = mysqli_query($this->connection, $Q);

        if (!$result) {
            throw new Exception('Query failed: ' . mysqli_error($this->connection));
        }

        return $result;
    }

    public function Disconnect()
    {
        if ($this->isConnected && $this->connection) {
            mysqli_close($this->connection);
            $this->isConnected = false;
        }
    }

    /**
     * @throws Exception
     */
    public function FetchLastInsertId()
    {
        $this->ensureConnected();
        return mysqli_insert_id($this->connection);
    }

    public function EscapeString($string): string
    {
        $this->ensureConnected();
        return mysqli_real_escape_string($this->connection, $string);
    }

    /**
     * @throws Exception
     */
    private function ensureConnected()
    {
        if (!$this->isConnected) {
            $this->connect();
        }
    }


}
