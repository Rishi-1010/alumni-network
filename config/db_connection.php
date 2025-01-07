<?php
// Check if session is not already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private $host = "localhost";
    private $db_name = "alumni_network";
    private $username = "root";
    private $password = "";
    private $conn;

    // Get database connection
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
    }

    // Register new user
    public function registerUser($fullname, $email, $phone, $password) {
        try {
            // Check if email exists
            $stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if($stmt->rowCount() > 0) {
                return ["status" => "error", "message" => "Email already exists"];
            }

            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
            
            if($stmt->execute([$fullname, $email, $phone, $hashed_password])) {
                return [
                    "status" => "success",
                    "message" => "Registration successful",
                    "user_id" => $this->conn->lastInsertId()
                ];
            }

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Login user
    public function loginUser($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(password_verify($password, $user['password'])) {
                    // Update last login
                    $this->updateLastLogin($user['user_id']);
                    
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    
                    return ["status" => "success", "message" => "Login successful"];
                }
            }
            
            return ["status" => "error", "message" => "Invalid email or password"];

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update last login time
    private function updateLastLogin($user_id) {
        $stmt = $this->conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    // Add educational details
    public function addEducation($user_id, $university, $enrollment, $graduation_year) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO educational_details (user_id, university_name, enrollment_number, graduation_year) VALUES (?, ?, ?, ?)");
            
            if($stmt->execute([$user_id, $university, $enrollment, $graduation_year])) {
                return ["status" => "success", "message" => "Educational details added successfully"];
            }

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Update professional status
    public function updateStatus($user_id, $status, $company = null, $position = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO professional_status (user_id, current_status, company_name, position) VALUES (?, ?, ?, ?)");
            
            if($stmt->execute([$user_id, $status, $company, $position])) {
                return ["status" => "success", "message" => "Status updated successfully"];
            }

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}

// Usage example:

// $db = new Database();
// $conn = $db->connect();

// // For registration:
// $result = $db->registerUser("John Doe", "john@example.com", "1234567890", "password123");

// // For login:
// $result = $db->loginUser("john@example.com", "password123");

// // For adding education:
// $result 