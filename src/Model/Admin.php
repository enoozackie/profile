<?php
namespace Lourdian\BasicStudent\Model;

use Lourdian\BasicStudent\Core\Database;
use Exception;

class Admin extends Database {

    // 🔑 Admin login
    public function login($username, $password) {
        try {
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
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }

    // 🧾 Register new admin
    public function register($username, $password, $fullname): bool {
        try {
            if (empty($username) || empty($password) || empty($fullname)) {
                return false;
            }

            $stmt = $this->conn->prepare("SELECT id FROM admins WHERE username=? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("INSERT INTO admins (username, password, fullname) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed, $fullname);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Admin registration error: " . $e->getMessage());
            return false;
        }
    }

    // 📋 Get all students
    public function getAllStudents(): array {
        try {
            $result = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Get all students error: " . $e->getMessage());
            return [];
        }
    }

    // 🔍 Get student by ID
    public function getStudentById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Get student by ID error: " . $e->getMessage());
            return null;
        }
    }

    // ❌ Delete student
    public function deleteStudent($id): bool {
        try {
            $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows === 0) {
                return false;
            }
            
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Delete student error: " . $e->getMessage());
            return false;
        }
    }

    // 🔄 Reset user password by ID or username
    public function resetUserPassword($identifier, $newPassword): bool {
        try {
            if (empty($identifier) || empty($newPassword)) {
                return false;
            }

            // Determine if identifier is ID or username
            if (is_numeric($identifier)) {
                $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
                $stmt->bind_param("i", $identifier);
            } else {
                $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
                $stmt->bind_param("s", $identifier);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                return false;
            }

            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateStmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
            $updateStmt->bind_param("si", $hashed, $user['id']);
            return $updateStmt->execute();
        } catch (Exception $e) {
            error_log("Reset user password error: " . $e->getMessage());
            return false;
        }
    }

    // ✏️ Update student
    public function updateStudent($id, $data): bool {
        try {
            $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows === 0) {
                return false;
            }
            
            $required = ['fullname', 'username', 'address', 'contact', 'birthday', 'sex'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return false;
                }
            }
            
            $stmt = $this->conn->prepare(
                "UPDATE users 
                 SET fullname=?, username=?, address=?, contact=?, birthday=?, sex=? 
                 WHERE id=?"
            );
            $stmt->bind_param(
                "ssssssi",
                $data['fullname'],
                $data['username'],
                $data['address'],
                $data['contact'],
                $data['birthday'],
                $data['sex'],
                $id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Update student error: " . $e->getMessage());
            return false;
        }
    }

    // 🔢 Count students
    public function countStudents(): int {
        try {
            $result = $this->conn->query("SELECT COUNT(*) as total FROM users");
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Count students error: " . $e->getMessage());
            return 0; 
        }
    }

    // 👤 Get admin by ID
    public function getById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Get admin by ID error: " . $e->getMessage());
            return null;
        }
    }
}
?>
