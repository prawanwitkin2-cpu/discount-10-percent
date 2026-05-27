# Implementation Plan: Discount 10 Percent (MVC & Security Upgrade)

อัปเดตล่าสุด: 27 พฤษภาคม 2026

## 1) เป้าหมายโปรเจกต์ (ปรับปรุงใหม่)
อัปเกรดสถาปัตยกรรมของโปรเจกต์เป็นแบบ MVC (Controllers, Services, Repositories, Middleware, Views) และเพิ่มฟีเจอร์สำคัญ:
- การ Login ฝั่งผู้ใช้ด้วย Google (Auto-register) เพื่อเก็บสถิติรายบุคคล
- เก็บ Audit Logs สำหรับ Admin
- ระบบ Reports สำหรับสถิติแบบละเอียด
- เพิ่มระบบความปลอดภัย (CSRF, Session Security, Validator)
- จัดโครงสร้างโฟลเดอร์ใหม่ให้เป็นระบบ คล้าย Laravel แต่ยังคงเป็น Native PHP + PDO

> [!WARNING]
> **ข้อจำกัดที่ต้องระวัง**:
> - ห้ามทำให้ระบบเดิมพัง และต้องรักษาโทน UI คาเฟ่มินิมอล
> - ห้ามลบไฟล์เดิมโดยไม่จำเป็น ให้เน้นย้ายและจัดหมวดหมู่

---

## 2) User Review Required / Open Questions

> [!IMPORTANT]
> **คำถามที่ต้องการการตัดสินใจจากผู้ใช้ก่อนเริ่มเขียนโค้ด:**
> 1. **Google Login Dependency**: เนื่องจากโปรเจกต์ตั้งใจไม่พึ่งพา Composer เพื่อให้อัปโหลดขึ้น Shared Host ได้ง่าย การเชื่อมต่อ Google OAuth2 แบบไม่มี Composer จะต้องเขียน Request ยิงผ่าน `curl` ไปยัง Google API โดยตรง หรือจะอนุญาตให้ใช้ Composer เพื่อโหลด `google/apiclient` ได้ครับ? (แนะนำให้ใช้ Composer เฉพาะฝั่ง Server แล้วรัน `composer dump-autoload` เพื่อให้โค้ดดูแลง่ายขึ้น แต่ถ้าบังคับห้ามใช้เลย จะเขียนแบบ cURL ให้ครับ)
> 2. **ตาราง `users`**: กรณีผู้ใช้ Login ด้วย Google สำเร็จ ระบบจะดึง `email` จาก Google หากเปลี่ยนสำนักในฟอร์ม จะให้อัปเดต `department_id` ในตาราง `users` ด้วยเสมอหรือไม่?

---

## 3) โครงสร้างโฟลเดอร์ใหม่เป้าหมาย

```text
TermWhan-TermKhom/
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── departments/ (index, create, edit, delete)
│   ├── branches/ (index, create, edit, delete)
│   ├── records/ (index, view)
│   └── reports/ (index, export)
├── app/
│   ├── Controllers/ (เช่น GoogleAuthController, RecordController, AdminController)
│   ├── Services/ (เช่น GoogleAuthService, ReportService)
│   ├── Repositories/ (เช่น UserRepository, RecordRepository)
│   ├── Middleware/ (เช่น AuthMiddleware, AdminMiddleware, CsrfMiddleware)
│   ├── Views/ (ไฟล์เทมเพลต HTML/PHP)
│   ├── bootstrap.php
│   ├── db.php
│   ├── helpers.php
│   ├── validator.php
│   ├── logger.php
│   └── csrf.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── security.php
│   └── oauth.php (ตั้งค่า Client ID / Secret ของ Google)
├── database/
│   ├── migrations/ (ไฟล์ SQL ปรับโครงสร้างใหม่)
│   └── seeders/
├── public/
│   └── assets/ (css, js, images)
├── storage/
│   ├── logs/
│   ├── exports/
│   └── backups/
├── tests/
├── index.php (Entry point สำหรับ Router เบื้องต้น หรือเรียก Controller)
├── auth/
│   ├── google-login.php
│   └── google-callback.php
├── .env.example
├── .htaccess
├── README.md
├── project_status.md
└── implementation_plan.md
```

---

## 4) แผนการปรับปรุงฐานข้อมูล (Migrations)

เราต้องเพิ่มตารางและคอลัมน์ใหม่:

### 4.1 `users` (สร้างใหม่)
- `id` (PK)
- `google_id` (VARCHAR)
- `email` (VARCHAR, UNIQUE)
- `name` (VARCHAR)
- `profile_picture` (TEXT)
- `department_id` (FK)
- `created_at`
- `updated_at`
- `last_login_at`

### 4.2 `discount_records` (แก้ไข)
- `user_id` (FK ไปยัง `users` อนุญาตให้ NULL ในข้อมูลเก่า)
- ลบ `nickname` ออก (ใช้ชื่อจาก users แทน) หรือคงไว้เผื่อแก้ไขหน้างาน

### 4.3 `admin_audit_logs` (สร้างใหม่)
- `id` (PK)
- `admin_id` (FK)
- `action` (VARCHAR) เช่น `CREATE_DEPARTMENT`, `DELETE_RECORD`
- `target_type` (VARCHAR)
- `target_id` (INT)
- `payload_json` (TEXT)
- `created_ip`
- `created_at`

---

## 5) ขั้นตอนการดำเนินงาน (Phases)

### Phase 1: เตรียมโครงสร้างและฐานข้อมูล (Foundation)
- สร้างโฟลเดอร์ใหม่ (`Controllers`, `Services`, `Repositories`, `Middleware`, `Views`)
- เพิ่มไฟล์ `validator.php`, `logger.php`, `csrf.php`
- จัดการสร้าง `.env.example` และ `config/` ใหม่
- สร้างไฟล์ `database/migrations/01_add_users_and_audit.sql` เพื่อรันเพิ่มตาราง `users` และ `admin_audit_logs`

### Phase 2: ปรับปรุงโครงสร้าง (Refactoring Existing Logic)
- **Database Connection**: รวมศูนย์ไว้ที่ `app/db.php`
- **Helpers & Validator**: ย้ายตรรกะการตรวจสอบค่าไปที่ `validator.php`
- **Views**: ย้ายโค้ด HTML บางส่วนใน `index.php` และหน้า `admin/*.php` ไปไว้ที่ `app/Views/`
- ทำการปรับปรุง (Refactor) หน้า Admin เดิม (`dashboard`, `records`, `departments`) ให้ไปเรียกใช้ `Repositories` เพื่อดึงข้อมูล แทนการเขียน SQL ปะปนในหน้า UI โดยตรง
- รับประกันว่า **Admin Panel เดิมไม่พัง**

### Phase 3: การเพิ่ม Google Login
- นำ `GoogleAuthService` และ `GoogleAuthController` มาวางโครง
- สร้างไฟล์ `auth/google-login.php` และ `auth/google-callback.php`
- เมื่อผู้ใช้กด Login ตรวจสอบใน `users` ว่ามี email นี้หรือไม่ ถ้าไม่มีให้ insert (Auto-register)
- เมื่อ Login สำเร็จ ส่งกลับไปที่ `index.php` พร้อม Session `user_id`

### Phase 4: แก้ไข Flow การบันทึกสิทธิ์ (Public Record)
- หน้า public/index.php ตรวจสอบว่าถ้ายังไม่ login ด้วย Google ให้โชว์ปุ่ม Login with Google
- ถ้า login แล้ว ให้ข้าม Step กรอกชื่อเล่น (ใช้ชื่อจาก Google Profile แทน)
- ตอนบันทึกลง `discount_records` ให้ insert `user_id` ไปด้วย

### Phase 5: Security & Audit
- เปิดใช้ CSRF Token ทุกฟอร์ม POST
- เขียน Helper สำหรับสร้างและตรวจ CSRF Token
- อัปเดตตรรกะ Admin หน้า CRUD (เช่น เพิ่ม/แก้ไขสำนัก) ให้เรียกใช้ `logger.php` บันทึกลง `admin_audit_logs`
- ทำหน้า Reports ดึงข้อมูลสถิติแยกตาม User

---

## 6) Verification Plan

- ตรวจสอบ `php -l` ของไฟล์ใหม่ทั้งหมด
- รันไฟล์ migration บน XAMPP เพื่อยืนยันว่า Database โครงสร้างสมบูรณ์
- ทดสอบระบบ Login ด้วย Google (Mock หรือเชื่อม API จริง) เพื่อให้มั่นใจว่า User ถูกสร้างลงฐานข้อมูล
- เข้า Admin Dashboard เพื่อยืนยันว่าโครงสร้างใหม่ไม่ทำให้หน้าเดิมแครช
- สร้าง record ด้วยตัวเองผ่านหน้าเว็บและตรวจเช็คใน `discount_records`
