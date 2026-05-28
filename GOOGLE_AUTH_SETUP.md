# คู่มือการขอ Google Client ID สำหรับระบบล็อกอิน (OAuth 2.0)

เอกสารนี้อธิบายขั้นตอนการขอ `Client ID` และ `Client Secret` จาก Google Cloud Console ฟรี เพื่อนำมาใช้กับระบบล็อกอินของโปรเจกต์

## สรุปขั้นตอนแบบรวบรัด (Overview)
1. สร้างโปรเจกต์ใน Google Cloud
2. ตั้งค่าหน้าจอขออนุญาต (OAuth Consent Screen)
3. สร้าง Client ID สำหรับ Web Application
4. นำค่ามาใส่ในไฟล์ `.env` ของโปรเจกต์

---

## ขั้นตอนที่ 1: สร้างโปรเจกต์ใหม่
1. ไปที่ [Google Cloud Console](https://console.cloud.google.com/) แล้วล็อกอินด้วยบัญชี Google ของคุณ
2. หากเพิ่งเข้าใช้งานครั้งแรก ให้กดยอมรับข้อตกลง (Agree & continue) **และหากระบบบังคับให้กรอกบัตรเครดิต ให้ปิดหน้านั้นแล้วเข้า URL ด้านบนใหม่ เพื่อข้ามการผูกบัตรได้เลย** (ระบบนี้ฟรี ไม่จำเป็นต้องผูกบัตร)
3. ด้านซ้ายบน (ข้างโลโก้ Google Cloud) คลิกปุ่มเลือกโปรเจกต์ แล้วกด **"NEW PROJECT"**
4. ตั้งชื่อโปรเจกต์ (เช่น `Discount-Admin-Login`) แล้วกดปุ่ม **"CREATE"**
5. รอสักครู่ แล้วเลือกโปรเจกต์ที่คุณเพิ่งสร้างขึ้นมา

---

## ขั้นตอนที่ 2: ตั้งค่าหน้าจอขออนุญาต (OAuth Consent Screen)
1. ในหน้าหลัก ให้มองหาและคลิกปุ่มสีน้ำเงิน **"Get started"** ตรงกลางจอ (ในส่วนของ Google Auth Platform) หรือไปที่เมนูซ้ายมือ เลือก **APIs & Services > OAuth consent screen**
2. เลือกประเภท Audience เป็น **"External"** (เพื่อให้บัญชี @gmail.com ทั่วไปเข้าได้) แล้วกดปุ่ม **"Next"**
3. ในหน้า **App Information** ให้กรอกข้อมูลดังนี้:
   - **App name:** ชื่อเว็บที่จะแสดงตอนล็อกอิน (เช่น `Discount 10 Percent`)
   - **User support email:** เลือกอีเมลของคุณจาก Dropdown
4. เลื่อนลงล่างสุดที่ส่วน **Developer contact information**:
   - ใส่อีเมลของคุณลงไป
5. กดปุ่ม **"Create"** (หรือ Save and Continue ไปเรื่อยๆ จนจบกระบวนการ)

---

## ขั้นตอนที่ 3: สร้าง Client ID
1. ไปที่เมนูทางซ้ายมือ เลือก **"Clients"** (หรือ **Credentials**)
2. กดปุ่ม **"+ Create client"** (หรือ + CREATE CREDENTIALS > OAuth client ID)
3. เลือก **Application type** เป็น **"Web application"**
4. ตั้งชื่อ Client Name (เช่น `Localhost Admin`)
5. เลื่อนลงมาที่หัวข้อ **Authorized redirect URIs** (สำคัญมาก)
   - กดปุ่ม **"+ ADD URI"**
   - พิมพ์ลิงก์นี้ลงไปให้เป๊ะ: `http://localhost:8000/auth/google-callback.php`
   - *(หากในอนาคตนำเว็บขึ้นออนไลน์ ให้กลับมาเพิ่ม URL จริงที่นี่ด้วย เช่น `https://www.yourdomain.com/auth/google-callback.php`)*
6. กดปุ่ม **"Create"**

---

## ขั้นตอนที่ 4: นำค่ามาตั้งค่าในโปรเจกต์
หลังจากกด Create ระบบจะแสดงหน้าต่างที่มีค่า **Client ID** และ **Client Secret** ขึ้นมา

1. คัดลอกค่าทั้งสองเอาไว้
2. กลับมาที่โปรเจกต์ของคุณ เปิดไฟล์ `.env`
3. นำค่าที่ได้มาวางต่อท้ายตัวแปร ดังนี้:

```env
GOOGLE_CLIENT_ID=วาง_Client_ID_ยาวๆ_ที่นี่.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=วาง_Client_Secret_ที่นี่
```

4. กดบันทึกไฟล์ (Save)

> **🎉 หมายเหตุ:** หลังจากนี้คุณสามารถใช้งานระบบ Google Login ผ่าน URL ของคุณ (เช่น http://localhost:8000/admin/login.php) ได้ทันที!
