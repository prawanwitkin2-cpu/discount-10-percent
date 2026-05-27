# Project Status

อัปเดตล่าสุด: 27 พฤษภาคม 2026

## Current Focus
ปรับปรุงโครงสร้างสถาปัตยกรรมโปรเจกต์เป็น MVC แบบ Native PHP และเพิ่มฟังก์ชัน Google OAuth Login ฝั่งผู้ใช้ทั่วไป พร้อม Audit Logs และระบบความปลอดภัย (เสร็จสิ้น Phase 1-6)

## Latest Progress
- อนุมัติการใช้ Composer และติดตั้ง `google/apiclient` เรียบร้อยแล้ว (ผ่าน XAMPP PHP CLI)
- สร้าง `.env` และตัวโหลดผ่าน `app/bootstrap.php` เพื่อดึงค่า API Keys และ DB Credentials
- สร้างตาราง `users` (รองรับ Google Login) และ `admin_audit_logs` เรียบร้อยแล้ว
- ย้าย/สร้างโครงสร้างโฟลเดอร์ MVC: `Controllers/`, `Services/`, `Repositories/`, `Middleware/`, `Views/` 
- เพิ่ม `app/logger.php`, `app/csrf.php`, `app/validator.php` และทำการ Refactor โค้ดเดิมให้เชื่อมต่อด้วย
- นำระบบ Login ด้วย Google มาใช้ที่หน้า Public (`index.php`) 
  - ซ่อนแบบฟอร์มหากยังไม่ Login
  - Auto-register ครั้งแรก
  - บันทึก `user_id` ลงในตาราง `discount_records`
  - หากผู้ใช้เลือกสำนัก ให้จำ `department_id` ไว้ที่ตาราง `users` ด้วย
- ตรวจสอบความถูกต้องของ Admin Panel โดยให้ยังสามารถใช้งานและ Login ได้ด้วยตัวเองแบบเดิม แต่เพิ่ม Audit Logs เข้าไปตอนสร้าง/แก้/ลบ สำนัก
- จัดการ `php -l` ทดสอบ Syntax และไม่พบ Error

## Files Modified
- `.gitignore`
- `.env` และ `.env.example`
- `composer.json` และ `composer.lock`
- `app/bootstrap.php`
- `app/records.php`
- `app/db.php`
- `app/helpers.php`
- `app/csrf.php`, `app/logger.php`, `app/validator.php`
- `app/Controllers/GoogleAuthController.php`
- `app/Services/GoogleAuthService.php`
- `auth/google-login.php` และ `auth/google-callback.php`
- `config/database.php`, `config/oauth.php`
- `database/migrations/01_add_users_and_audit.sql`
- `index.php`

## Testing Status
- Syntax Check (ผ่าน `php -l`)
- Database Import (ผ่าน)

## Immediate Next Steps
1. ทดสอบยิง Google Login จริง (ต้องใส่ `GOOGLE_CLIENT_ID` และ `SECRET` ใน `.env`)
2. เริ่มทำส่วนของ `admin/reports/` (Reports System) เพื่อ Export ข้อมูลสถิติตาม User ID (Phase ถัดไป)
