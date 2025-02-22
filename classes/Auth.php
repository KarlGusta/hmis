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

            $this->db->conn->begin_transaction();

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
                "INSERT INTO users (id, username, email, password_hash, first_name, last_name, status, department_id)
                 VALUES (?, ?, ?, ?, ?, ?, 'active', ?)"
            );

            if (!$stmt) {
                error_log("MySQL Prepare Error: " . $this->db->conn->error);
                throw new Exception("Database error: " . $this->db->conn->error);
            }

            // Add debug logging
            error_log("Preparing to insert user with data: " . print_r($data, true));

            $department_id = !empty($data['department_id']) ? $data['department_id'] : null;
            
            try {
                $stmt->bind_param("sssssss", 
                    $user_id, 
                    $data['username'], 
                    $data['email'], 
                    $password_hash, 
                    $data['first_name'],
                    $data['last_name'],
                    $department_id
                );
            } catch (Exception $e) {
                error_log("Bind Param Error: " . $e->getMessage());
                throw new Exception("Database error during parameter binding: " . $e->getMessage());
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to create user account");
            }

            // Insert user roles
            foreach ($data['roles'] as $role_name) {
                $stmt = $this->db->conn->prepare(
                    "INSERT INTO user_roles (user_id, role_id) 
                     SELECT ?, id FROM roles WHERE name = ?"
                );
                
                if (!$stmt) {
                    throw new Exception("Failed to prepare role assignment statement");
                }
                
                $stmt->bind_param("ss", $user_id, $role_name);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to assign role: " . $role_name);
                }
            }

            // If doctor role is included, create doctor record
            if (in_array('doctor', $data['roles'])) {
                $stmt = $this->db->conn->prepare(
                    "INSERT INTO doctors (
                        user_id, first_name, last_name, gender, date_of_birth,
                        national_id, license_number, specialization, qualification,
                        experience_years, consultation_fee, department_id, status,
                        bio, photo, office_number, available_days, available_times
                    ) VALUES (
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, 'active',
                        ?, ?, ?, ?, ?
                    )"
                );

                // Convert experience_years to integer and consultation_fee to float
                $experience_years = intval($data['experience_years']);
                $consultation_fee = floatval($data['consultation_fee']);
                
                // Set default values for optional fields
                $bio = $data['bio'] ?? null;
                $photo = $data['photo'] ?? null;
                $office_number = $data['office_number'] ?? null;
                
                // Format available_days and available_times
                $available_days = !empty($data['available_days']) ? 
                    json_encode(array_map('trim', explode(',', $data['available_days']))) : 
                    null;
                
                $available_times = null;
                if (!empty($data['available_times'])) {
                    // Validate and format time range
                    $times = explode('-', $data['available_times']);
                    if (count($times) === 2) {
                        $start = date('H:i:s', strtotime(trim($times[0])));
                        $end = date('H:i:s', strtotime(trim($times[1])));
                        $available_times = json_encode(['start' => $start, 'end' => $end]);
                    }
                }

                $stmt->bind_param(
                    "sssssssssidssssss",
                    $user_id,
                    $data['first_name'],
                    $data['last_name'],
                    $data['gender'],
                    $data['date_of_birth'],
                    $data['national_id'],
                    $data['license_number'],
                    $data['specialization'],
                    $data['qualification'],
                    $experience_years,
                    $consultation_fee,
                    $department_id,
                    $bio,
                    $photo,
                    $office_number,
                    $available_days,
                    $available_times
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to create doctor record: " . $stmt->error);
                }

                // Create doctor contact information
                $doctor_id = $this->db->conn->insert_id;
                $stmt = $this->db->conn->prepare(
                    "INSERT INTO doctor_contacts (
                        doctor_id, phone, email, emergency_contact_name,
                        emergency_contact_phone, address, city, country
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $emergency_contact_name = $data['emergency_contact_name'] ?? null;
                $emergency_contact_phone = $data['emergency_contact_phone'] ?? null;
                $address = $data['address'] ?? null;
                $city = $data['city'] ?? null;
                $country = $data['country'] ?? null;

                $stmt->bind_param(
                    "ssssssss",
                    $doctor_id,
                    $data['phone'],
                    $data['email'],
                    $emergency_contact_name,
                    $emergency_contact_phone,
                    $address,
                    $city,
                    $country
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to create doctor contact information: " . $stmt->error);
                }
            }

            $this->db->conn->commit();
            return $user_id;
        } catch (Exception $e) {
            $this->db->conn->rollback();
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function login($username, $password)
    {
        try {
            // Get user by username or email
            $stmt = $this->db->conn->prepare(
                "SELECT u.id, u.username, u.email, u.password_hash, u.status, u.first_name, u.last_name,
                        GROUP_CONCAT(r.name) as roles
                 FROM users u
                 LEFT JOIN user_roles ur ON u.id = ur.user_id
                 LEFT JOIN roles r ON ur.role_id = r.id
                 WHERE (u.username = ? OR u.email = ?)
                 GROUP BY u.id"
            );
            
            // Check if prepare statement failed
            if ($stmt === false) {
                throw new Exception("Database error: " . $this->db->conn->error);
            }

            $stmt->bind_param("ss", $username, $username);
            
            if (!$stmt->execute()) {
                throw new Exception("Database error: " . $stmt->error);
            }
            
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
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'roles' => $user['roles'] ? explode(',', $user['roles']) : []
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
