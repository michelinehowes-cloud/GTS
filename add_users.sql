-- إضافة مستخدمين تجريبيين لنظام تدريب الخريجين

-- المستخدم الإداري
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('مدير النظام', 'admin@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW(), NOW());

-- منسق التدريب
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('منسق التدريب', 'training@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'training_coordinator', 1, NOW(), NOW(), NOW());

-- مسؤول الشراكات
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('مسؤول الشراكات', 'partnership@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'partnership_officer', 1, NOW(), NOW(), NOW());

-- مسؤول الإرشاد المهني
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('مسؤول الإرشاد المهني', 'guidance@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'career_guidance_officer', 1, NOW(), NOW(), NOW());

-- مسؤول التقييم والمتابعة
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('مسؤول التقييم والمتابعة', 'evaluation@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'evaluation_followup', 1, NOW(), NOW(), NOW());

-- خريج تجريبي
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('خريج تجريبي', 'graduate@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'graduate', 1, NOW(), NOW(), NOW());

-- ممثل شركة
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('ممثل شركة تجريبية', 'company@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company', 1, NOW(), NOW(), NOW());

-- مسؤول الميديا
INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
VALUES ('مسؤول الميديا', 'media@tripoliuniversity.edu.ly', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'media_officer', 1, NOW(), NOW(), NOW());

-- ملاحظة: كلمة المرور لجميع المستخدمين هي "password"
