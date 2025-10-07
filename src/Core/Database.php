<?php
namespace Lourdian\BasicStudent\Core;

use mysqli;
use Exception;

class Database {
    protected $conn;

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "basic_student";

        $this->conn = new mysqli($host, $user, $pass, $db);

        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
    }
}
