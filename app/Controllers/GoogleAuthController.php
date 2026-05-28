<?php
declare(strict_types=1);

require_once BASE_PATH . '/app/Services/GoogleAuthService.php';

class GoogleAuthController
{
    private GoogleAuthService $googleService;

    public function __construct()
    {
        $this->googleService = new GoogleAuthService();
    }

    public function redirect(): void
    {
        $url = $this->googleService->getAuthUrl();
        header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
        exit;
    }

    public function handleCallback(string $code): void
    {
        $profile = $this->googleService->authenticate($code);
        
        if (!$profile) {
            $_SESSION['login_error'] = 'เกิดข้อผิดพลาดในการดึงข้อมูลจาก Google';
            redirect('/admin/login.php');
            return;
        }

        $email = $profile['email'];
        
        // Check if this email is an admin
        if (attempt_admin_login_by_email($email)) {
            // Login successful
            redirect('/admin/dashboard.php');
            return;
        }

        // Login failed (Not an admin)
        $_SESSION['login_error'] = "อีเมลนี้ ($email) ไม่มีสิทธิ์เข้าใช้งานระบบแอดมิน";
        redirect('/admin/login.php');
    }
}
