<?php
namespace Lourdian\BasicStudent\Model;

use Lourdian\BasicStudent\Core\Database;
use Lourdian\BasicStudent\Core\Crud;

class User extends Database implements Crud {

    public function create($data) {
        $sql = "INSERT INTO users (username, password, fullname, address, birthday, contact, sex) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bind_param(
            "sssssss",
            $data['username'],
            $hashed,
            $data['fullname'],
            $data['address'],
            $data['birthday'],
            $data['contact'],
            $data['sex']
        );

        return $stmt->execute();
    }

    public function read($id) {
        return $this->getById($id);
    }

    public function update($id, $data) {
        $sql = "UPDATE users 
                SET username=?, fullname=?, address=?, birthday=?, contact=?, sex=? 
                WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $data['username'],
            $data['fullname'],
            $data['address'],
            $data['birthday'],
            $data['contact'],
            $data['sex'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Login
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }

    // Logout
    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Get all users (optional)
    public function getAll() {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single user
    public function getById($id) {
        $sql = "SELECT id, username, fullname, address, birthday, contact, sex 
                FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
