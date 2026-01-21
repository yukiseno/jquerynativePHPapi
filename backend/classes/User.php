<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Register a new user
     */
    public function register($name, $email, $password)
    {
        // Validate input
        if (!$name || !$email || !$password) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        // Check if email already exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        try {
            $now = date('Y-m-d H:i:s');
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hashedPassword, $now, $now]);

            return ['success' => true, 'message' => 'Account created successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        // Validate input
        if (!$email || !$password) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        // Find user by email
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'These credentials do not match our records'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'These credentials do not match our records'];
        }

        // Generate token
        $token = $this->generateToken($user['id']);

        // Format user response
        $userData = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'address' => $user['address'],
            'city' => $user['city'],
            'country' => $user['country'],
            'zip_code' => $user['zip_code'],
            'phone_number' => $user['phone_number'],
            'profile_image' => $user['profile_image'],
            'profile_completed' => $user['profile_completed'],
            'two_factor_enabled' => $user['two_factor_enabled'],
        ];

        return [
            'success' => true,
            'user' => $userData,
            'access_token' => $token
        ];
    }

    /**
     * Generate API token
     */
    private function generateToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        try {
            $timestampFunc = $this->db->getCurrentTimestampFunction();
            $stmt = $this->db->prepare(
                "INSERT INTO personal_access_tokens (tokenable_id, tokenable_type, name, token, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, {$timestampFunc}, {$timestampFunc})"
            );
            $stmt->execute([$userId, 'App\\Models\\User', 'user_token', $hashedToken]);

            return $token;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verify token and get user
     */
    public function verifyToken($token)
    {
        if (!$token) {
            return null;
        }

        $hashedToken = hash('sha256', $token);

        try {
            $timestampFunc = $this->db->getCurrentTimestampFunction();
            $stmt = $this->db->prepare(
                "SELECT tokenable_id FROM personal_access_tokens 
                 WHERE token = ? AND (expires_at IS NULL OR expires_at > {$timestampFunc})"
            );
            $stmt->execute([$hashedToken]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            // Get user data
            $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$result['tokenable_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Logout user (revoke token)
     */
    public function logout($token)
    {
        if (!$token) {
            return ['success' => false, 'message' => 'Invalid token'];
        }

        $hashedToken = hash('sha256', $token);

        try {
            $stmt = $this->db->prepare('DELETE FROM personal_access_tokens WHERE token = ?');
            $stmt->execute([$hashedToken]);

            return ['success' => true, 'message' => 'Logged out successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Logout failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get user by ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return null;
            }

            return [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'address' => $user['address'],
                'city' => $user['city'],
                'country' => $user['country'],
                'zip_code' => $user['zip_code'],
                'phone_number' => $user['phone_number'],
                'profile_image' => $user['profile_image'],
                'profile_completed' => $user['profile_completed']
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        try {
            $timestampFunc = $this->db->getCurrentTimestampFunction();
            $stmt = $this->db->prepare("
                UPDATE users 
                SET phone_number = ?, address = ?, city = ?, country = ?, zip_code = ?, profile_completed = 1, updated_at = {$timestampFunc}
                WHERE id = ?
            ");

            $stmt->execute([
                $data['phoneNumber'] ?? '',
                $data['address'] ?? '',
                $data['city'] ?? '',
                $data['country'] ?? '',
                $data['zip'] ?? '',
                $userId
            ]);

            // Return updated user info
            return $this->findById($userId);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate 2FA secret and return QR code URL
     */
    public function generate2FASecret($email)
    {
        require_once __DIR__ . '/TOTP.php';

        $secret = TOTP::generateSecret();
        $qrCodeURL = TOTP::getQRCodeURL($secret, $email);

        return [
            'secret' => $secret,
            'qrCodeURL' => $qrCodeURL
        ];
    }

    /**
     * Enable 2FA for a user
     */
    public function enable2FA($userId, $secret, $verificationCode)
    {
        require_once __DIR__ . '/TOTP.php';

        // Verify the code is correct
        if (!TOTP::verify($secret, $verificationCode)) {
            return ['success' => false, 'message' => 'Invalid verification code'];
        }

        // Save the secret to the database
        try {
            $stmt = $this->db->prepare(
                'UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?'
            );
            $stmt->execute([$secret, $userId]);

            return ['success' => true, 'message' => '2FA enabled successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to enable 2FA'];
        }
    }

    /**
     * Disable 2FA for a user
     */
    public function disable2FA($userId)
    {
        try {
            $stmt = $this->db->prepare(
                'UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = ?'
            );
            $stmt->execute([$userId]);

            return ['success' => true, 'message' => '2FA disabled successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to disable 2FA'];
        }
    }

    /**
     * Verify 2FA code during login
     */
    public function verify2FACode($userId, $code)
    {
        require_once __DIR__ . '/TOTP.php';

        try {
            $stmt = $this->db->prepare('SELECT two_factor_secret FROM users WHERE id = ? AND two_factor_enabled = 1');
            $stmt->execute([$userId]);
            $result = $stmt->fetch();

            if (!$result) {
                return false;
            }

            return TOTP::verify($result['two_factor_secret'], $code);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if user has 2FA enabled
     */
    public function has2FAEnabled($userId)
    {
        try {
            $stmt = $this->db->prepare('SELECT two_factor_enabled FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $result = $stmt->fetch();

            return $result && $result['two_factor_enabled'] == 1;
        } catch (Exception $e) {
            return false;
        }
    }
}
