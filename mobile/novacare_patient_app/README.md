# NovaCare Patient Mobile App MVP

Ứng dụng di động (Flutter Mobile App) dành cho Bệnh nhân trong Hệ thống quản lý bệnh viện NovaCare.

---

## 📌 Công nghệ & Cấu trúc
* **Framework:** Flutter SDK (kênh Stable)
* **Quản lý trạng thái:** `provider` (MVVM/Notifier pattern)
* **Xác thực:** JWT Access/Refresh Token (Refresh Token Rotation) tích hợp **Đăng nhập Sinh trắc học** (Face ID & Vân tay qua `local_auth`).
* **Kết nối API:** `dio` tích hợp Interceptor tự động làm mới token, tự động điều hướng khi hết hạn, và **cấu hình địa chỉ máy chủ động (Base URL)** trong Settings.
* **Lưu trữ dữ liệu:** `flutter_secure_storage` (Mã hóa keychain trên iOS & EncryptedSharedPreferences trên Android).
* **Thanh toán cổng:** `webview_flutter` cổng VNPay Sandbox.
* **Tư vấn triệu chứng:** Trợ lý ảo y tế thông minh (AI Medical Chatbot) với bong bóng chat Messenger, gửi tin nhanh qua gợi ý câu hỏi, cảnh báo đỏ và medical disclaimer.
* **Đa nhiệm:** Đọc/Ghi trạng thái thông báo có icon phân loại, Polling 30s lượt xếp hàng khám thời gian thực.

---

## 🚀 Hướng dẫn cài đặt & Chạy ứng dụng

### 1. Chuẩn bị môi trường
* Đảm bảo Java Development Kit (JDK) và Android SDK đã được cài đặt và cấu hình đường dẫn.
* Khởi động máy ảo Android (Emulator) hoặc kết nối điện thoại Android thật qua cổng USB Debugging.

### 2. Cấu hình IP Máy chủ
* **Chạy máy ảo Android Emulator:** Mặc định base URL trỏ về máy ảo là `http://10.0.2.2/NovaCare/api/v1` (Đã cấu hình trong [api_constants.dart](lib/core/constants/api_constants.dart)).
* **Chạy thiết bị thật / Mạng LAN:**
  1. Mở file [api_constants.dart](lib/core/constants/api_constants.dart).
  2. Thay đổi `10.0.2.2` thành địa chỉ IP máy tính chạy WAMP/XAMPP của bạn (Ví dụ: `http://192.168.1.15/NovaCare/api/v1`).
  3. Đảm bảo cấu hình CORS trên backend chấp nhận thiết bị kết nối.

### 3. Tải thư viện phụ thuộc
Truy cập thư mục `mobile/novacare_patient_app` và chạy lệnh:
```bash
flutter pub get
```

### 4. Khởi chạy ứng dụng
Chạy lệnh sau để khởi động chế độ debug trên thiết bị máy ảo:
```bash
flutter run -d emulator-5554
```

---

## 📂 Sơ đồ cấu trúc thư mục dự án
```
lib/
├── core/
│   ├── constants/
│   │   └── api_constants.dart
│   ├── network/
│   │   ├── api_client.dart
│   │   └── api_exception.dart
│   ├── storage/
│   │   └── secure_storage_service.dart
│   ├── theme/
│   │   └── app_theme.dart
│   └── widgets/
│       ├── empty_state.dart
│       ├── error_view.dart
│       └── loading_widget.dart
├── routes/
│   └── app_routes.dart
├── features/
│   ├── auth/
│   │   ├── auth_notifier.dart
│   │   ├── login_screen.dart
│   │   ├── register_screen.dart
│   │   └── splash_screen.dart
│   ├── home/
│   │   ├── home_notifier.dart
│   │   └── home_screen.dart
│   ├── profile/
│   │   ├── profile_notifier.dart
│   │   └── profile_screen.dart
│   ├── departments/
│   │   ├── departments_notifier.dart
│   │   └── departments_screen.dart
│   ├── doctors/
│   │   ├── doctors_notifier.dart
│   │   └── doctors_screen.dart
│   ├── appointments/
│   │   ├── appointment_notifier.dart
│   │   ├── appointment_detail_screen.dart
│   │   ├── book_appointment_screen.dart
│   │   └── my_appointments_screen.dart
│   ├── queue/
│   │   ├── queue_notifier.dart
│   │   └── queue_tracking_screen.dart
│   ├── records/
│   │   ├── record_notifier.dart
│   │   ├── record_detail_screen.dart
│   │   └── medical_records_screen.dart
│   ├── prescriptions/
│   │   ├── prescription_notifier.dart
│   │   ├── prescription_detail_screen.dart
│   │   └── prescriptions_screen.dart
│   ├── invoices/
│   │   ├── invoice_notifier.dart
│   │   ├── invoice_detail_screen.dart
│   │   └── invoices_screen.dart
│   ├── payments/
│   │   └── vnpay_webview_screen.dart
│   ├── ai_chat/
│   │   ├── ai_chat_notifier.dart
│   │   └── ai_chat_screen.dart
│   ├── notifications/
│   │   ├── notification_notifier.dart
│   │   └── notifications_screen.dart
│   └── settings/
│       └── settings_screen.dart
├── app.dart
└── main.dart
```
