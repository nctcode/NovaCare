# Triển khai sửa lỗi Tuần 1 — NovaCare

## Mục tiêu
Sửa tất cả lỗi Critical + Important đã phát hiện trong báo cáo rà soát.

## Proposed Changes

### 1. Security Core — Tạo helper bảo mật
#### [NEW] helpers/Security.php
- Hàm `hashPassword()` / `verifyPassword()` wrapper cho bcrypt
- Hàm `generateCsrfToken()` / `validateCsrfToken()` 
- Hàm `requireRole($allowedRoles)` — kiểm tra phân quyền
- Hàm `requirePost()` — enforce POST method
- Hàm `sanitize($input)` — trim + htmlspecialchars

---

### 2. Singleton Database
#### [MODIFY] [database.php](file:///c:/wamp64/www/CNM1/config/database.php)
- Chuyển sang Singleton pattern, tránh tạo 6-8 connection/page

---

### 3. Hash Password + Login
#### [MODIFY] [AuthController.php](file:///c:/wamp64/www/CNM1/controllers/AuthController.php)
- Dùng `password_verify()` thay `===`
- Thêm CSRF check cho login form

#### [MODIFY] [Patient.php](file:///c:/wamp64/www/CNM1/models/Patient.php)  
- `create()`: hash password trước khi INSERT

#### [MODIFY] [Doctor.php](file:///c:/wamp64/www/CNM1/models/Doctor.php)
- `create()`: hash password trước khi INSERT

#### [NEW] migrate_hash_passwords.php
- Script chạy 1 lần để hash tất cả password hiện tại trong DB

---

### 4. CSRF Token
#### [MODIFY] [index.php](file:///c:/wamp64/www/CNM1/index.php)
- Require `helpers/Security.php` ở entry point

#### Tất cả view có form POST sẽ cần thêm CSRF hidden field — tuy nhiên thay vì sửa từng view (20+ file), sẽ tạo helper function `csrfField()` và inject vào layout.

---

### 5. Phân quyền Controller
#### [MODIFY] Tất cả controller có action nhạy cảm:
- `PatientController`: store/update/delete → chỉ admin
- `DoctorController`: store/update/delete → chỉ admin
- `NurseController`: store/update/delete → chỉ admin
- `AppointmentController`: updateStatus → admin/doctor; store → patient/admin
- `ShiftController`: store/create → admin; register/unregister → doctor
- `InvoiceController`: create/store/markPaid/cancel → admin
- `MedicineController`: store/update/delete → admin
- `PrescriptionController`: create/store → doctor
- `InpatientController`: admit/storeAdmit/discharge → admin
- `EquipmentController`: store/update/delete → admin
- `ServiceController`: store/update/delete → admin
- `DepartmentController`: store/update/delete → admin

---

### 6. Sửa bug `treatment` + Migration
#### [NEW] migrate_fix_treatment.php
- `ALTER TABLE medical_records ADD COLUMN treatment TEXT AFTER diagnosis`

---

### 7. Bảo vệ Migration Files
#### [NEW] .htaccess (root)
- Block truy cập trực tiếp vào `add_*.php`, `migrate_*.php`

---

### 8. Validate Logic — Appointments
#### [MODIFY] [Appointment.php](file:///c:/wamp64/www/CNM1/models/Appointment.php)
- Thêm `checkDuplicate($doctorId, $date)` — kiểm tra trùng lịch

#### [MODIFY] [AppointmentController.php](file:///c:/wamp64/www/CNM1/controllers/AppointmentController.php)
- `store()`: validate ngày không ở quá khứ, kiểm tra trùng lịch
- `updateStatus()`: chuyển sang POST

---

### 9. Chuyển hành động nhạy cảm sang POST
#### [MODIFY] Controllers:
- `AppointmentController::updateStatus()` → POST
- `InvoiceController::markPaid()`, `cancel()` → POST
- `InpatientController::discharge()` → POST
- `PatientController::delete()` → POST

---

### 10. Fix GeminiAI constants
#### [MODIFY] [ai.php](file:///c:/wamp64/www/CNM1/config/ai.php)
- Thêm define cho `GEMINI_API_KEY`, `GEMINI_MODEL`, `GEMINI_API_URL` (giá trị rỗng/mặc định)

---

## Verification Plan
- Kiểm tra login vẫn hoạt động sau hash password
- Kiểm tra tạo bệnh nhân/bác sĩ mới với password đã hash
- Kiểm tra CSRF token trên form
- Kiểm tra phân quyền: patient không thể xóa bệnh nhân
- Kiểm tra tạo bệnh án với cột treatment
- Kiểm tra migration files không truy cập được từ browser

> [!IMPORTANT]
> Sau khi chạy `migrate_hash_passwords.php`, tất cả password cũ sẽ được hash. User cần dùng password gốc (`123456`) để login — password_verify sẽ so sánh đúng.
