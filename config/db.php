<?php

class Database
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct()
    {
        $this->host = "localhost:3306";
        $this->username = "root";
        $this->password = "";
        $this->database = "webphp_ec";
    }

    public function getConnection()
    {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            return $this->conn;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}
//test connection to database
$db = new Database();
$conn = $db->getConnection();
if ($conn == null) {
    echo "Connection failed";
    die();
} else {
    echo "Connection success";
}
