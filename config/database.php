<?php
class DatabaseConnection {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'hospital_management';
    public $conn;

    public function __construct() {
        // Create connection
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Patient ID Generator
    public function generatePatientID() {
        $prefix = 'PT';
        $timestamp = date('YmdHis');
        $randomNum = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $randomNum;
    }

    // Error logging
    public function logError($error) {
        $errorLog = fopen('system_errors.log', 'a');
        fwrite($errorLog, date('Y-m-d H:i:s') . " - " . $error . "\n");
        fclose($errorLog);
    }

    public function executeQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $this->conn->error);
        }
        
        if ($params) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            $stmt->bind_param($types, ...$values);
        }
        
        $stmt->execute();
        return $stmt->insert_id ?: true;
    }

    public function fetchAll($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if ($params) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $param;
            }
            
            $stmt->bind_param($types, ...$values);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchOne($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            // Add error handling
            throw new Exception("Query preparation failed: " . $this->conn->error);
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function beginTransaction() {
        $this->conn->begin_transaction();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function rollback() {
        $this->conn->rollback();
    }

    public function __destruct() {
        // Close connection
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function getErrorInfo() {
        return $this->conn->error;
    }
}
?>