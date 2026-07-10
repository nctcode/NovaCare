# Task Tracker — NovaCare System Review

## Giai đoạn 1: Database (🔴 Critical)
- [/] 1.1 Migration MyISAM → InnoDB + Foreign Keys + Indexes
- [ ] 1.2 Sửa cột `treatment` trong `medical_records`
- [ ] 1.3 Tạo bảng `activity_logs`

## Giai đoạn 2: Business Logic (🟡 Important)
- [ ] 2.1 Trừ tồn kho thuốc + kiểm tra hạn sử dụng
- [ ] 2.2 Validate discount hóa đơn
- [ ] 2.3 Hash password Nurse
- [ ] 2.4 Fix XSS ConsultationController
- [ ] 2.5 Fix InpatientController truy vấn DB trực tiếp

## Giai đoạn 3: Performance & Audit (🟡 Important)
- [ ] 3.1 Tối ưu query Shift (LEFT JOIN)
- [ ] 3.2 Tạo AuditLog helper
- [ ] 3.3 Tích hợp Audit Log vào controllers
- [ ] 3.4 Enforce rule ca trực 2 night/tuần

## Giai đoạn 4: Bảo mật bổ sung
- [ ] 4.1 Fix GeminiAI SSL verification
- [ ] 4.2 Dọn dẹp MeetingController
- [ ] 4.3 Chuyển API Key Gemini sang header

## Giai đoạn 5: AI Improvements
- [ ] 5.1 Refactor BaseAI
- [ ] 5.2 Kích hoạt AI Summarize
