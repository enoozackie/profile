<?php
namespace Lourdian\BasicStudent\Model;

use Lourdian\BasicStudent\Core\Database;
use Lourdian\BasicStudent\Core\Crud;

class Auth extends Database implements Crud {

    public function login($username, $password) {
        try {
            // Check in users table
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user['password'])) {
                $user['role'] = $user['role'] ?? 'user';
                return $user;
            }

            // Check in admins table
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $admin['role'] = 'admin';
                return $admin;
            }

            return false;
        } catch (Exception $e) {
            error_log("Auth login error: " . $e->getMessage());
            return false;
        }
    }

    // CRUD interface implementation
    public function create($data) {
        try {
            $sql = "INSERT INTO users (username, password, fullname, address, birthday, contact, sex, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
            $stmt = $this->conn->prepare($sql);
            
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt->bind_param(
                "sssssss",
                $data['username'],
                $hashedPassword,
                $data['fullname'],
                $data['address'],
                $data['birthday'],
                $data['contact'],
                $data['sex']
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Auth create error: " . $e->getMessage());
            return false;
        }
    }

    public function read($where = []) {
        try {
            $sql = "SELECT * FROM users";
            $params = [];
            $types = "";
            
            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                    $params[] = $value;
                    $types .= "s";
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Auth read error: " . $e->getMessage());
            return [];
        }
    }

    public function update($data, $where) {
        try {
            $sql = "UPDATE users SET ";
            $setParts = [];
            $params = [];
            $types = "";
            
            foreach ($data as $key => $value) {
                $setParts[] = "$key = ?";
                $params[] = $value;
                $types .= "s";
            }
            $sql .= implode(", ", $setParts);
            
            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                    $params[] = $value;
                    $types .= "s";
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Auth update error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($where) {
        try {
            $sql = "DELETE FROM users";
            $params = [];
            $types = "";
            
            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                    $params[] = $value;
                    $types .= "s";
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $stmt = $this->conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Auth delete error: " . $e->getMessage());
            return false;
        }
    }

    // Admin registration
    public function registerAdmin($username, $password, $fullname) {
        try {
            $admin = new Admin();
            return $admin->register($username, $password, $fullname);
        } catch (Exception $e) {
            error_log("Admin registration error: " . $e->getMessage());
            return false;
        }
    }
}
?>