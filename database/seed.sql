INSERT INTO departments (name) VALUES
('สำนักตัวอย่าง 1'),
('สำนักตัวอย่าง 2');

INSERT INTO branches (name) VALUES
('สาขาตัวอย่าง 1'),
('สาขาตัวอย่าง 2');

-- เปลี่ยนรหัสผ่านทันทีหลังติดตั้ง
-- ค่าเริ่มต้น: admin / admin123
INSERT INTO admins (username, password_hash, display_name) VALUES
('admin', '$2y$10$itMuBDXu57fPEEcS06jOe.IXyAuw0C65bWWZxVlGsGOC.8YwdMIly', 'Administrator');
