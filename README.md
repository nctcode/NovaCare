ôi 

# 🏥 NovaCare – Hệ Thống Quản Lý Bệnh Viện Thông Minh 4.0

NovaCare là hệ thống quản lý bệnh viện thông minh toàn diện (Smart Hospital Management System) được phát triển bằng ngôn ngữ PHP thuần (Vanilla PHP) theo mô hình MVC hiện đại. Hệ thống tích hợp các công nghệ tiên tiến như Trợ lý AI (Google Gemini, OpenAI compatible Beeknoee, Ollama), hệ thống giám sát thời gian thực, tích hợp cổng thanh toán trực tuyến (VNPay Sandbox, MoMo) và hệ thống phân quyền chặt chẽ theo vai trò (RBAC).

---

## 🏗️ Kiến Trúc Hệ Thống

Hệ thống được thiết kế theo mô hình **Monolithic MVC** tinh gọn, đảm bảo hiệu năng cao và dễ dàng triển khai.

```mermaid
graph TD
    User([Người dùng / Actor]) -->|HTTP Request| Index[index.php - Front Controller]
    Index -->|Route mapping| Routes[routes.php]
    Routes -->|Dispatch| Controllers[Controllers]
  
    subgraph Core App Layer
        Controllers -->|Gọi xử lý| Models[Models & AI Services]
        Controllers -->|Gửi dữ liệu| Views[Views]
        Controllers -->|Kiểm tra bảo mật| Helpers[Helpers: Security, AuditLog]
    end

    subgraph Data & Services
        Models -->|Truy vấn (Singleton)| DB[(Database: MySQL InnoDB)]
        Models -->|Gọi API| AI[AI Services: Ollama, Gemini, Beeknoee]
    end

    Views -->|Render HTML/CSS/JS| User
```

### 💻 Stack Công Nghệ Chính

* **Backend:** PHP 8.2+ (hỗ trợ tốt trên Apache).
* **Database:** MySQL 8.0+ sử dụng Storage Engine **InnoDB** hỗ trợ toàn vẹn tham chiếu (Foreign Keys), Triggers và Transactions.
* **AI Services:**
  * **Ollama API:** Xử lý mô hình ngôn ngữ lớn chạy cục bộ (Local LLM như `qwen2`, `llama3`).
  * **Gemini API & Beeknoee API:** Tích hợp mô hình đám mây hiệu năng cao (`gemini-2.0-flash`, `gpt-5`).
* **Frontend:** HTML5, CSS3, Javascript thuần (đảm bảo giao diện hiện đại, mượt mà và trực quan).
* **Deployment:** Docker & Docker Compose để thiết lập nhanh môi trường chạy.

---

## 👥 Phân Vai Trò & Tính Năng Cốt Lõi (Actors & Usecases)

Hệ thống hỗ trợ 9 vai trò người dùng (Actor) với luồng nghiệp vụ khép kín từ khâu tiếp đón đến thanh toán và cấp phát thuốc:

### 1. Bệnh Nhân (Patient)

* **Đặt lịch hẹn:** Đăng ký lịch khám trực tuyến với bác sĩ và chuyên khoa phù hợp.
* **Quản lý EMR cá nhân:** Xem lịch sử bệnh án điện tử cá nhân, đơn thuốc và kết quả xét nghiệm.
* **Theo dõi hàng chờ:** Xem số thứ tự khám trực tuyến theo thời gian thực (Real-time Queue).
* **Thanh toán số:** Thanh toán hóa đơn viện phí thông qua cổng **VNPay Sandbox** hoặc **MoMo**.

### 2. Lễ Tân / Tiếp Đón (Receptionist)

* **Tiếp nhận & Đăng ký:** Tạo hồ sơ bệnh nhân mới đến khám trực tiếp.
* **Quản lý lịch hẹn:** Duyệt hoặc hủy các lịch hẹn đăng ký trực tuyến.
* **Điều phối hàng chờ (Queue):** Cấp số thứ tự khám và phân bệnh nhân vào các phòng khám chuyên khoa.

### 3. Bác Sĩ (Doctor)

* **Khám bệnh chuyên khoa:** Xem danh sách hàng chờ bệnh nhân tại phòng khám của mình.
* **Chẩn đoán lâm sàng:** Tra cứu bệnh án cũ, nhập thông tin khám mới kèm mã hóa bệnh theo chuẩn **ICD-10 Autocomplete**.
* **Trợ lý AI gợi ý:** Nhận tư vấn từ AI về chẩn đoán, phác đồ điều trị và cảnh báo chỉ số sinh hiệu (Vitals Alert).
* **Chỉ định cận lâm sàng:** Kê đơn thuốc điện tử, yêu cầu xét nghiệm (Lab Orders) hoặc chỉ định điều trị nội trú.

### 4. Điều Dưỡng (Nurse)

* **Quản lý nội trú (Inpatient):** Đón nhận bệnh nhân nhập viện, phân buồng và giường bệnh.
* **Theo dõi sinh hiệu (Vitals):** Cập nhật chỉ số sinh hiệu hàng ngày của bệnh nhân nội trú.
* **Cảnh báo AI:** Hệ thống AI tự động phát hiện và cảnh báo tức thời khi các chỉ số sinh hiệu ở mức nguy kịch.

### 5. Kỹ Thuật Viên Xét Nghiệm (Technician)

* **Nhận chỉ định:** Xem danh sách yêu cầu xét nghiệm cận lâm sàng từ bác sĩ.
* **Cập nhật kết quả:** Ghi nhận và tải kết quả xét nghiệm lên hồ sơ EMR của bệnh nhân.

### 6. Dược Sĩ (Pharmacist)

* **Quản lý đơn thuốc:** Tiếp nhận và duyệt các đơn thuốc đã được thanh toán.
* **Cấp phát thuốc:** Trừ số lượng tồn kho tự động sau khi cấp phát thuốc thành công.
* **Quản lý kho dược:** Theo dõi tồn kho, cảnh báo thuốc sắp hết hạn sử dụng.

### 7. Thu Ngân (Cashier)

* **Tính hóa đơn:** Tạo hóa đơn tổng hợp từ tiền khám, cận lâm sàng, thuốc và giường bệnh.
* **Áp dụng BHYT:** Tra cứu và tự động chiết khấu bảo hiểm y tế của bệnh nhân trước khi xuất hóa đơn.
* **Xác nhận thanh toán:** Xác nhận thanh toán hóa đơn bằng tiền mặt hoặc thanh toán điện tử tại quầy.

### 8. Giám Đốc (Director)

* **Dashboard vận hành:** Giám sát doanh thu, công suất giường bệnh và lượng bệnh nhân đến khám.
* **Dự báo tải AI:** Sử dụng AI để dự báo lưu lượng bệnh nhân và tải hệ thống trong 7 ngày kế tiếp nhằm tối ưu hóa nhân sự.

### 9. Quản Trị Viên (Admin)

* **Quản lý người dùng (RBAC):** Cấp phát tài khoản, quản lý vai trò chi tiết theo đặc tả hệ thống.
* **Cấu trúc danh mục:** Thiết lập thông tin khoa phòng, danh mục thuốc, dịch vụ kỹ thuật và thiết bị y tế.
* **Nhật ký hệ thống (Audit Logs):** Theo dõi mọi thao tác dữ liệu (Ai làm gì, lúc nào, giá trị cũ/mới) để phục vụ kiểm toán bảo mật.

---

## 🤖 Kiến Trúc & Tích Hợp AI

Hệ thống tích hợp lớp AI Service linh hoạt kế thừa từ lớp trừu tượng [BaseAI](file:///d:/wamp64/www/NovaCare/models/BaseAI.php):

```
                       ┌─────────────────────────┐
                       │      BaseAI (Model)     │
                       └────────────┬────────────┘
                                    │
         ┌──────────────────────────┼──────────────────────────┐
         ▼                          ▼                          ▼
┌─────────────────┐        ┌─────────────────┐        ┌─────────────────┐
│    OllamaAI     │        │    GeminiAI     │        │   BeeknoeeAI    │
│ (Local Llama3)  │        │ (Gemini 2.0 Fl.)│        │ (gpt-5/GPT-4)   │
└─────────────────┘        └─────────────────┘        └─────────────────┘
```

* **Trò chuyện & Chẩn đoán sơ bộ:** AI phân tích triệu chứng của bệnh nhân, phân loại mức độ khẩn cấp (`low`, `medium`, `high`) và tự động đề xuất chuyên khoa khám phù hợp dưới dạng cấu trúc JSON sạch.
* **Tóm tắt bệnh án:** Gọi API tóm tắt toàn bộ lịch sử bệnh án dài của bệnh nhân bằng cấu trúc Markdown có tổ chức cho bác sĩ đọc nhanh trước khi khám.
* **Dự báo vận hành:** Dự đoán dòng tiền, lượt khám hỗ trợ Ban giám đốc điều phối hoạt động.

---

## 🔐 Cơ Chế Bảo Mật & Tối Ưu

1. **Bảo mật mật khẩu:** Toàn bộ mật khẩu người dùng được mã hóa bằng thuật toán `bcrypt` mạnh thông qua lớp hỗ trợ [Security](file:///d:/wamp64/www/NovaCare/helpers/Security.php).
2. **CSRF Protection:** Tất cả các hành động ghi hoặc sửa đổi dữ liệu (POST request) đều được bắt buộc đính kèm và kiểm tra CSRF token nhằm chống tấn công giả mạo yêu cầu.
3. **IDOR Protection:** Kiểm tra quyền sở hữu dữ liệu cấp dòng (Data-Level Security). Đảm bảo Bệnh nhân/Bác sĩ không thể truy cập hoặc sửa đổi trái phép dữ liệu của người khác bằng cách so sánh khóa ngoại liên kết với Session.
4. **Database Connection Singleton:** Tối ưu hóa hiệu năng bằng cách chỉ mở duy nhất một kết nối PDO duy nhất đến cơ sở dữ liệu trong suốt chu trình chạy của một Request thông qua [Database Singleton](file:///d:/wamp64/www/NovaCare/config/database.php).
5. **Audit Logging:** Toàn bộ các thao tác `INSERT`, `UPDATE`, `DELETE` hay `LOGIN` đều được lưu tự động thông tin chi tiết (bao gồm IP, ID người dùng, dữ liệu cũ dạng JSON, dữ liệu mới dạng JSON) vào bảng `audit_logs`.

---

## 📁 Cấu Trúc Thư Mục Project

```bash
NovaCare/
├── assets/                 # Các file tĩnh (CSS, JS, hình ảnh)
├── config/                 # Các file cấu hình hệ thống
│   ├── ai.php              # Cấu hình API key, Model, System prompt của AI
│   └── database.php        # Kết nối CSDL dạng Singleton
├── controllers/            # Lớp Điều khiển (Xử lý Request & Route mapping)
│   ├── AuthController.php  # Xử lý đăng nhập, đăng ký
│   ├── PatientController.php
│   ├── ShiftController.php # Đăng ký và quản lý ca trực nhân viên
│   └── ...
├── helpers/                # Thư viện tiện ích dùng chung
│   ├── Security.php        # Hash mật khẩu, CSRF, IDOR, RBAC
│   ├── AuditLog.php        # Ghi log hoạt động hệ thống
│   ├── VNPayHelper.php     # Hỗ trợ tích hợp cổng thanh toán VNPay
│   └── MailHelper.php      # Gửi email thông báo đặt lịch
├── libs/                   # Các thư viện bên thứ ba
├── migrations/             # Các file SQL cập nhật cơ sở dữ liệu theo phiên bản
├── models/                 # Lớp Mô hình (Tương tác database & AI API)
│   ├── BaseAI.php          # Class trừu tượng định nghĩa các dịch vụ AI
│   ├── GeminiAI.php        # Tích hợp Gemini Cloud API
│   ├── OllamaAI.php        # Tích hợp Ollama Local API
│   ├── BeeknoeeAI.php      # Tích hợp OpenAI-compatible Beeknoee API
│   ├── Patient.php
│   ├── Appointment.php
│   └── ...
├── views/                  # Lớp Hiển thị giao diện (chia theo module & role)
│   ├── layout/             # Các view khung (header, footer, sidebar)
│   ├── dashboard/          # Trang chủ sau đăng nhập của các vai trò
│   ├── appointments/       # Giao diện quản lý lịch hẹn
│   └── ...
├── env.php                 # Lưu trữ API key và biến môi trường nhạy cảm (không đẩy lên Git)
├── index.php               # Front Controller - Entry Point duy nhất của ứng dụng
├── routes.php              # Định nghĩa các Route liên kết url với Controller
├── final.sql               # File Dump cơ sở dữ liệu đầy đủ
├── Dockerfile              # Dockerfile build Apache + PHP 8.2 environment
└── docker-compose.yml      # Thiết lập môi trường chạy App + MySQL + phpMyAdmin nhanh
```

---

## 🚀 Hướng Dẫn Cài Đặt & Khởi Chạy

### Cách 1: Sử dụng Docker & Docker Compose (Khuyên dùng)

Yêu cầu máy tính đã cài đặt **Docker Desktop**.

1. Mở terminal tại thư mục gốc của project.
2. Sao chép và cấu hình file `env.php` (nhập API key nếu có):
   ```php
   <?php
   return [
       'BEEKNOEE_API_KEY' => 'sk-bee-xxxx...',
       'GEMINI_API_KEY' => 'AIzaSyxxxx...'
   ];
   ```
3. Chạy lệnh khởi dựng container:
   ```bash
   docker-compose up -d --build
   ```
4. Sau khi khởi động thành công:
   * **Ứng dụng Web:** Truy cập tại địa chỉ [http://localhost:8000](http://localhost:8000)
   * **phpMyAdmin:** Quản lý cơ sở dữ liệu tại [http://localhost:8080](http://localhost:8080) (Tài khoản: `root` / Mật khẩu: `root`)

### Cách 2: Triển khai trên WAMP / XAMPP Server

1. Copy thư mục `NovaCare` vào thư mục web root (ví dụ: `C:/wamp64/www/NovaCare` hoặc `C:/xampp/htdocs/NovaCare`).
2. Tạo cơ sở dữ liệu mới trong phpMyAdmin tên là `hospital_management` với bảng mã `utf8mb4_unicode_ci`.
3. Import file cơ sở dữ liệu [final.sql](file:///d:/wamp64/www/NovaCare/final.sql) vào cơ sở dữ liệu vừa tạo.
4. Cấu hình file `env.php` ở thư mục gốc (nhập thông tin API Key cho AI nếu cần sử dụng tính năng thông minh).
5. Truy cập ứng dụng qua trình duyệt theo đường dẫn cục bộ (ví dụ: `http://localhost/NovaCare`).

---

## 📈 Lộ Trình Phát Triển & Bảo Trì (Roadmap)

Chi tiết các tác vụ cần hoàn thiện được quản lý tập trung trong file [task.md](file:///d:/wamp64/www/NovaCare/task.md):

* **Giai đoạn 1:** Tối ưu hóa Database (MyISAM sang InnoDB), bổ sung các Index cho cột tìm kiếm thường xuyên.
* **Giai đoạn 2:** Nâng cao Business Logic (Khấu trừ kho thuốc tự động khi kê đơn, kiểm tra hạn sử dụng, chống lỗi XSS trong view).
* **Giai đoạn 3:** Ghi log hoạt động nâng cao và kiểm duyệt ràng buộc ca trực của nhân sự.
* **Giai đoạn 4:** Bảo mật kết nối API AI (Gemini SSL verification, chuyển API Key sang header an toàn).
* **Giai đoạn 5:** Refactor cấu trúc AI Service dùng chung thông qua `BaseAI`.
