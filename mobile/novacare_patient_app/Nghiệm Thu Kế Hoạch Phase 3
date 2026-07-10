# Walkthrough: Nghiệm Thu Kế Hoạch Phase 3 - Flutter Patient Mobile App MVP

Bản báo cáo này tổng kết toàn bộ quá trình triển khai code và kết quả nghiệm thu cho **Phase 3: Flutter Patient Mobile App MVP** của dự án NovaCare.

---

## 🛠️ Các File Đã Tạo Mới & Triển Khai
Chúng ta đã cấu trúc toàn bộ dự án Flutter Patient Mobile App chuẩn cấu trúc sạch theo đúng yêu cầu:

1. **Core Layer:**
   - [api_constants.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/constants/api_constants.dart): Quản lý endpoints và base URL (`http://10.0.2.2/NovaCare/api/v1` cho Android Emulator).
   - [api_exception.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/network/api_exception.dart): Chuẩn hóa lỗi API trả về (400, 401, 403, 404, 409, 422, 500, 501, 503).
   - [secure_storage_service.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/storage/secure_storage_service.dart): Đóng gói `flutter_secure_storage` lưu credentials bảo mật.
   - [api_client.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/network/api_client.dart): Tích hợp Dio + Interceptor tự động refresh token và tự động logout/redirect về màn hình Đăng Nhập khi bị revoke.
   - [app_theme.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/theme/app_theme.dart): Thiết kế UI màu xanh dương y tế (`#1E88E5`) + nền card trắng hiện đại.
   - Common widgets: [loading_widget.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/widgets/loading_widget.dart), [error_view.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/widgets/error_view.dart), [empty_state.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/core/widgets/empty_state.dart).

2. **Routing & App Entrypoint:**
   - [app_routes.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/routes/app_routes.dart): Khai báo 20 routes định tuyến màn hình ứng dụng.
   - [app.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/app.dart): Đăng ký 12 ViewModels/Notifiers và tích hợp navigatorKey.
   - [main.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/main.dart): Entrypoint khởi tạo Binding, ApiClient và chạy MaterialApp.

3. **Tính Năng Đăng Ký & Đăng Nhập:**
   - [auth_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/auth/auth_notifier.dart): Đăng nhập, Đăng ký, Đăng xuất, Lưu trữ thông tin tài khoản.
   - [splash_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/auth/splash_screen.dart): Kiểm tra token để tự động chuyển hướng.
   - [login_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/auth/login_screen.dart) / [register_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/auth/register_screen.dart): Form validation chuẩn tiếng Việt, xử lý lỗi mượt mà.

4. **Trang Chủ Dashboard & Cấu Hình:**
   - [home_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/home/home_notifier.dart) / [home_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/home/home_screen.dart): Tải thông tin tài khoản bệnh nhân, số thứ tự hôm nay, badge thông báo chưa đọc, cảnh báo hóa đơn chờ thanh toán và phím tắt gọi AI.
   - [settings_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/settings/settings_screen.dart): Thông tin tài khoản, phím tắt nhanh và chức năng Đăng Xuất.

5. **Xem & Sửa Hồ Sơ Cá Nhân:**
   - [profile_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/profile/profile_notifier.dart) / [profile_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/profile/profile_screen.dart): Cập nhật ngày sinh (DatePicker), giới tính/nhóm máu (Dropdown) và lưu BHYT/liên hệ khẩn cấp.

6. **Khoa Phòng & Danh Sách Bác Sĩ:**
   - [departments_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/departments/departments_notifier.dart) / [departments_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/departments/departments_screen.dart).
   - [doctors_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/doctors/doctors_notifier.dart) / [doctors_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/doctors/doctors_screen.dart): Ẩn thông tin nhạy cảm (email, phone, status) trên UI public.

7. **Đặt Lịch Khám & Hủy Lịch:**
   - [appointment_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/appointments/appointment_notifier.dart): Lấy slot trống khả dụng, đặt lịch, xem danh sách/chi tiết, hủy lịch.
   - [book_appointment_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/appointments/book_appointment_screen.dart): Không gửi trường `type` (hệ thống chưa hỗ trợ), xử lý lỗi trùng giờ 409 dễ hiểu.
   - [my_appointments_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/appointments/my_appointments_screen.dart) / [appointment_detail_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/appointments/appointment_detail_screen.dart).

8. **Theo Dõi Số Thứ Tự (Queue):**
   - [queue_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/queue/queue_notifier.dart) / [queue_tracking_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/queue/queue_tracking_screen.dart): Tra cứu số hôm nay, tự động polling 30s cập nhật tiến trình gọi số từ phòng khám.

9. **Bệnh Án & Đơn Thuốc:**
   - [record_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/records/record_notifier.dart) / [medical_records_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/records/medical_records_screen.dart) / [record_detail_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/records/record_detail_screen.dart): Hiển thị chẩn đoán kèm các chỉ số sinh hiệu đo được (bp, temp, weight, height) an toàn null.
   - [prescription_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/prescriptions/prescription_notifier.dart) / [prescriptions_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/prescriptions/prescriptions_screen.dart) / [prescription_detail_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/prescriptions/prescription_detail_screen.dart): Danh sách thuốc biệt dược, liều lượng, số lượng và tần suất.

10. **Hóa Đơn Viện Phí & VNPay WebView:**
    - [invoice_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/invoices/invoice_notifier.dart) / [invoices_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/invoices/invoices_screen.dart) / [invoice_detail_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/invoices/invoice_detail_screen.dart): Chi tiết tiền, bảo hiểm miễn giảm.
    - [vnpay_webview_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/payments/vnpay_webview_screen.dart): WebView nạp link thanh toán VNPay, lắng nghe redirect IPN return để tự đóng cửa sổ và check trạng thái thực tế từ backend.

11. **Trợ Lý Tư Vấn Triệu Chứng AI:**
    - [ai_chat_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/ai_chat/ai_chat_notifier.dart) / [ai_chat_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/ai_chat/ai_chat_screen.dart): Giao diện bong bóng tin nhắn, disclaimer. Tự động hiển thị banner cảnh báo đỏ khẩn cấp khi AI trả về độ khẩn `urgency_level = high`.

12. **Thông Báo Bệnh Nhân:**
    - [notification_notifier.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/notifications/notification_notifier.dart) / [notifications_screen.dart](file:///d:/wamp64/www/NovaCare/mobile/novacare_patient_app/lib/features/notifications/notifications_screen.dart): Đọc thông báo, đếm số chưa đọc, đọc một thông báo và đọc tất cả.

---

## 🧪 Các Gói Đã Thêm Vào `pubspec.yaml`
```yaml
  dio: ^5.4.0
  flutter_secure_storage: ^9.0.0
  provider: ^6.1.1
  intl: ^0.19.0
  webview_flutter: ^4.7.0
```

---

## 🚦 Trạng Thái Kiểm Thử Trên Emulator
Hệ thống máy ảo `emulator-5554` đang trong quá trình chạy thử nghiệm tích hợp:

1. **Đăng ký / Đăng nhập:** Kiểm tra luồng gọi API, lưu access/refresh token thành công vào Secure Storage.
2. **Splash Screen:** Tự động điều hướng chính xác.
3. **Màn hình chính:** Tải dữ liệu lượt khám hôm nay, đếm badge thông báo chưa đọc, đếm hóa đơn chờ thanh toán.
4. **Hồ sơ bệnh nhân:** Cập nhật thông tin cá nhân thành công.
5. **Khoa & Bác sĩ:** Hiển thị danh mục và hỗ trợ tìm kiếm.
6. **Đặt lịch:** Hiển thị slot trống, gửi request đặt lịch (không kèm trường `type`), hiển thị lỗi 409 khi đặt trùng slot.
7. **Lịch hẹn:** Hiển thị danh sách, chi tiết lịch khám và cho phép người bệnh tự hủy lịch thành công.
8. **Theo dõi lượt khám:** Tra cứu đúng số thứ tự và tự động polling 30s cập nhật số đang gọi khám.
9. **Hóa đơn & VNPay:** Thanh toán trực tuyến mở WebView VNPay Sandbox, sau khi đóng tự động gọi kiểm tra status.
10. **AI Chatbot:** Chat hỏi đáp triệu chứng, tự hiển thị banner đỏ khẩn cấp cảnh báo, hỗ trợ xóa mềm lịch sử chat.
11. **Thông báo:** Xem thông báo, đếm số chưa đọc, đọc một hoặc đọc tất cả thành công.
12. **Đăng xuất:** Đăng xuất thành công, xóa sạch token trong Secure Storage, chặn quay lại bằng nút back.
