<?php
declare(strict_types=1);

require_once BASE_PATH . '/app/Services/GoogleAuthService.php';

class GoogleAuthController
{
    private GoogleAuthService $googleService;
    private PDO $db;

    public function __construct()
    {
        $this->googleService = new GoogleAuthService();
        $this->db = db();
    }

    public function redirect(): void
    {
        $url = $this->googleService->getAuthUrl();
        header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
        exit;
    }

    public function handleCallback(string $code): void
    {
        $userInfo = $this->googleService->authenticate($code);

        if (!$userInfo) {
            $_SESSION['error'] = 'Failed to authenticate with Google.';
            header('Location: /');
            exit;
        }

        $user = $this->findOrCreateUser($userInfo);
        
        // Update last_login_at
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$user['id']]);

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['department_id'] = $user['department_id']; // Can be null if new
        
        header('Location: /');
        exit;
    }

    private function findOrCreateUser(array $userInfo): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$userInfo['email']]);
        $user = $stmt->fetch();

        if ($user) {
            // Update profile picture and name if they changed
            $updateStmt = $this->db->prepare("UPDATE users SET name = ?, profile_picture = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$userInfo['name'], $userInfo['picture'], $user['id']]);
            return $user;
        }

        // Register new user
        $insertStmt = $this->db->prepare("
            INSERT INTO users (google_id, email, name, profile_picture) 
            VALUES (?, ?, ?, ?)
        ");
        $insertStmt->execute([
            $userInfo['google_id'],
            $userInfo['email'],
            $userInfo['name'],
            $userInfo['picture']
        ]);
        
        $newUserId = (int) $this->db->lastInsertId();
        
        return [
            'id' => $newUserId,
            'google_id' => $userInfo['google_id'],
            'email' => $userInfo['email'],
            'name' => $userInfo['name'],
            'department_id' => null
        ];
    }
}
