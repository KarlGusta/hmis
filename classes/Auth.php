<?php
class Auth
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($data)
    {
        try {
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Check if username or email already exists
            $stmt = $this->db->conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $data['username'], $data['email']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Username or email already exists");
            }

            // Hash password
            $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);

            // Generate UUID
            $user_id = $this->generateUUID();

            // Insert new user
            $stmt = $this->db->conn->prepare(
                "INSERT INTO users (id, username, email, password_hash, role, status, department_id)
                 VALUES (?, ?, ?, ?, ?, 'active', ?)"
            );

            $role = 'user'; // Default role
            $department_id = !empty($data['department_id']) ? $data['department_id'] : null;
            $stmt->bind_param("ssssss", $user_id, $data['username'], $data['email'], $password_hash, $role, $department_id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to create user account");
            }

            return $user_id;
        } catch (Exception $e) {
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function login($username, $password)
    {
        try {
            // Get user by username or email
            $stmt = $this->db->conn->prepare(
                "SELECT id, username, email, password_hash, role, status
                 FROM users
                 WHERE (username = ? OR email = ?)"
            );
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Invalid credentials");
            }

            $user = $result->fetch_assoc();

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                // Log failed attempt
                $this->logLoginAttempt($username, false);
                throw new Exception("Invalid credentials");
            }

            // Check if account is active
            if ($user['status'] !== 'active') {
                throw new Exception("Account is inactive");
            }

            // Update last login
            $stmt = $this->db->conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("s", $user['id']);
            $stmt->execute();

            // Log successful attempt
            $this->logLoginAttempt($username, true);

            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
        } catch (Exception $e) {
            throw new Exception("Login failed: " . $e->getMessage());
        }
    }

    private function logLoginAttempt($username, $success)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->db->conn->prepare(
            "INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $username, $ip, $success);
        $stmt->execute();
    }

    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
