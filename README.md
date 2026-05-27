# Discount 10 Percent

ระบบ PHP + MySQL สำหรับบันทึกการใช้สิทธิ์ส่วนลด 10% และดูข้อมูลผ่านหน้าแอดมิน

## Requirements
- PHP 8.x
- MySQL/MariaDB
- Apache shared hosting เช่น ByetHost

## Setup
1. สร้างฐานข้อมูล MySQL บนโฮส
2. import `database/schema.sql`
3. import `database/seed.sql`
4. แก้ค่าฐานข้อมูลใน `config/database.php`
5. เปิดหน้าเว็บที่ `index.php`
6. เข้าแอดมินที่ `/admin/login.php`

## Default Admin
- Username: `admin`
- Password: `admin123`

เปลี่ยนรหัสผ่านทันทีหลังติดตั้งจริง

## Current Features
- Public stepper สำหรับ PDPA, ชื่อเล่น/สำนัก, สาขา, จำนวนร้อน/เย็น/Delivery
- Server-side validation
- Admin login ด้วย PHP session
- Dashboard สรุปร้อน/เย็น/Delivery และแยกตามสำนัก
- ตารางข้อมูลทั้งหมดพร้อม pagination และเลือกแสดง 5/15/25/50 รายการ

