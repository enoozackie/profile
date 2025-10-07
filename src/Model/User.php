<?php
namespace Lourdian\BasicStudent\Model;
use Lourdian\BasicStudent\Core\Database;

class User extends Database
{
    // 📝 Create new user (Sign Up)
    public function create($data)
    {
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
        } catch (\Exception $e) {
            error_log("User creation error: " . $e->getMessage());
            return false;
        }
    }

    // 📝 Update user profile (including username)
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE users 
                    SET fullname = ?, username = ?, address = ?, birthday = ?, contact = ?, sex = ? 
                    WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "ssssssi",
                $data['fullname'],
                $data['username'],
                $data['address'],
                $data['birthday'],
                $data['contact'],
                $data['sex'],
                $id
            );
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("User update error: " . $e->getMessage());
            return false;
        }
    }

    // 🔄 Update user profile WITHOUT touching username
    public function updateProfileOnly($id, $data)
    {
        try {
            $sql = "UPDATE users 
                    SET fullname = ?, address = ?, birthday = ?, contact = ?, sex = ? 
                    WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sssssi",
                $data['fullname'],
                $data['address'],
                $data['birthday'],
                $data['contact'],
                $data['sex'],
                $id
            );
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("User updateProfileOnly error: " . $e->getMessage());
            return false;
        }
    }

    // 🔍 Get user by ID
    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return null;
        }
    }

    // 🔑 Login function
    public function login($username, $password)
    {
        try {
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['id'] = $user['id'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'] ?? '';
                return true;
            }

            return false;
        } catch (\Exception $e) {
            error_log("User login error: " . $e->getMessage());
            return false;
        }
    }

    // 🔄 Update password only
    public function updatePassword($id, $newPassword)
    {
        try {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Update password error: " . $e->getMessage());
            return false;
        }
    }
    // 🔍 Get all users
public function getAllUsers()
{
    try {
        $sql = "SELECT id, username, fullname, address, birthday, contact, sex, role FROM users ORDER BY id DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Get all users error: " . $e->getMessage());
        return [];
    }
}

}
?>
