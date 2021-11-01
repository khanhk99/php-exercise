<?php
class DBconnection
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "sql-exercise";
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername; dbname=$this->dbname", $this->username, $this->password);
        } catch (PDOException $e) {
            die("Connection failded: " . $e->getMessage());
        }
    }

    protected function executeNoParam($sql)
    {
        try {
            $query = $this->conn->prepare($sql);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);

            return $query->fetchAll();
        } catch (PDOException $e) {
        }
    }

    protected function executeParam($sql, $params)
    {
        try {
            $query = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $query->bindParam("$key", $value["data"], $value["type"]);
            }

            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);

            return $query->fetchAll();
        } catch (PDOException $e) {
        }
    }

    protected function modifyData($sql, $params)
    {
        try {
            $query = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $query->bindParam("$key", $value["data"], $value["type"]);
            }

            $query->execute();
            return $this->conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
