# 🏥 RÀ SOÁT HỆ THỐNG NovaCare – Y Tế Số 4.0

---

## 1. BẢO MẬT — 🔴 CRITICAL

### 1.1 Mật khẩu lưu PLAINTEXT

> [!CAUTION]
> Đây là lỗ hổng nghiêm trọng nhất của toàn bộ hệ thống.

**File:** [AuthController.php](file:///c:/wamp64/www/CNM1/controllers/AuthController.php#L34)

```php
// HIỆN TẠI — so sánh plaintext
if ($user && $user['password'] === $password) {
```

**SQL dump** lưu password `123456` dạng plaintext. Trong [Patient.php](file:///c:/wamp64/www/CNM1/models/Patient.php#L58) và [Doctor.php](file:///c:/wamp64/www/CNM1/models/Doctor.php#L61), tạo user mới cũng gán password plaintext.

**Sửa:**
```php
// Khi tạo user
$hashed = password_hash($data['password'], PASSWORD_BCRYPT);

// Khi login
if ($user && password_verify($password, $user['password'])) {
```

---

### 1.2 Không có CSRF Protection

Tất cả form POST (tạo bệnh nhân, đặt lịch, tạo hóa đơn...) đều **không có CSRF token**. Attacker có thể tạo form giả lừa admin submit.

**Sửa:** Sinh token vào session, nhúng `<input type="hidden" name="csrf_token">` vào mọi form, kiểm tra ở controller.

---

### 1.3 XSS tiềm ẩn trong view

[ai-assistant/index.php](file:///c:/wamp64/www/CNM1/views/ai-assistant/index.php#L65) dùng `htmlspecialchars()` đúng cách. Tuy nhiên, **ConsultationController** dòng 44–46 nhúng HTML trực tiếp vào `$_SESSION['success']` có chứa `$result['meeting_link']` — nếu dữ liệu bị chèn script, sẽ bị XSS khi render.

---

### 1.4 Hành động nhạy cảm qua GET

[AppointmentController.php](file:///c:/wamp64/www/CNM1/controllers/AppointmentController.php#L87-L101): `updateStatus()` dùng GET (`$_GET['id']`, `$_GET['status']`). Tương tự `markPaid()`, `cancel()` trong InvoiceController, `discharge()` trong InpatientController. Attacker chỉ cần gửi link là thực hiện được hành động.

**Sửa:** Chuyển sang POST + CSRF token.

---

### 1.5 API Key lộ trong URL (GeminiAI)

[GeminiAI.php](file:///c:/wamp64/www/CNM1/models/GeminiAI.php#L88): `?key=` nằm trên URL → lộ trong server log, browser history. Ngoài ra, `CURLOPT_SSL_VERIFYPEER => False` ở chat() (dòng 97) tắt xác minh SSL — dễ bị MITM. Trong `summarizeMedicalRecords()` lại đặt `true` → không nhất quán.

---

### 1.6 `config/ai.php` thiếu GEMINI_API_KEY & GEMINI_MODEL & GEMINI_API_URL

File [ai.php](file:///c:/wamp64/www/CNM1/config/ai.php) chỉ define Ollama nhưng `GeminiAI.php` vẫn tham chiếu `GEMINI_API_KEY`, `GEMINI_MODEL`, `GEMINI_API_URL`. Nếu ai load `GeminiAI.php`, sẽ bị **PHP Warning: Use of undefined constant**.

---

## 2. CƠ SỞ DỮ LIỆU

### 2.1 Dùng MyISAM — không hỗ trợ Foreign Key

> [!WARNING]
> Toàn bộ bảng dùng `ENGINE=MyISAM`. MyISAM **không enforce Foreign Key**, nghĩa là quan hệ chỉ là "trang trí". Có thể xóa doctor mà appointment vẫn trỏ tới ID không tồn tại.

**Sửa:** Chuyển sang `InnoDB`, thêm `FOREIGN KEY ... ON DELETE RESTRICT` hoặc `CASCADE`.

### 2.2 Thiếu cột quan trọng

| Bảng | Thiếu |
|------|-------|
| `medical_records` | `treatment` (code INSERT dùng nhưng SQL không có cột này) |
| `users` | `is_active`, `last_login`, `avatar` |
| `appointments` | `time_slot` (chỉ có datetime, không quản lý slot) |
| `shifts` | `start_time`, `end_time` (chỉ có day/night, không biết giờ cụ thể) |
| `medicines` | `unit`, `manufacturer`, `category` |
| `departments` | `head_doctor_id`, `phone`, `floor` |

### 2.3 Dư thừa / Sai chuẩn hóa

- `patients.medical_history` (TEXT) — lưu lịch sử bệnh dạng text tự do, không truy vấn được. Nên tham chiếu từ `medical_records`.
- `doctor_shifts` chỉ dành cho doctor. Nurse cũng cần ca trực nhưng bảng tên `doctor_shifts` → không mở rộng được.

### 2.4 Thiếu bảng audit/log

Không có bảng `activity_logs` để ghi lại ai làm gì, lúc nào. Đối với hệ thống y tế, đây là **yêu cầu pháp lý**.

---

## 3. LOGIC NGHIỆP VỤ

### 3.1 Ca trực (Shifts)

- **Rule "2 ca night/tuần"** có hàm `countNightShiftsInWeek()` nhưng **không được gọi** ở đâu. Controller `register()` không kiểm tra rule này → rule chỉ tồn tại trên giấy.
- Ca `day` không giới hạn số người → có thể đăng ký vô hạn.
- Nurse không thể đăng ký ca trực (controller chỉ tìm `doctorModel`).

### 3.2 Lịch khám (Appointments)

- **Không kiểm tra trùng lịch**: 2 bệnh nhân có thể đặt cùng giờ, cùng bác sĩ.
- **Không kiểm tra bác sĩ có ca trực**: Đặt lịch ngày bác sĩ không trực vẫn được.
- **Không validate ngày quá khứ**: Có thể đặt lịch vào ngày đã qua.
- `store()` không validate `doctor_id`, `appointment_date` rỗng hay không hợp lệ.

### 3.3 Bệnh án (Medical Records)

- `create()` trong model bind `:treatment` nhưng bảng `medical_records` trong SQL **không có cột `treatment`** → sẽ lỗi SQL khi tạo bệnh án.
- Không có chức năng **cập nhật** hoặc **xóa** bệnh án (đúng theo nghiệp vụ y tế không nên xóa, nhưng cần có bổ sung/sửa).

### 3.4 Đơn thuốc

- Khi kê đơn thuốc, **không trừ tồn kho** `medicines.quantity`.
- Không kiểm tra thuốc hết hạn (`expiry_date`) trước khi kê.

### 3.5 Hóa đơn

- `discount` nhập tự do, không giới hạn — có thể nhập âm hoặc lớn hơn `total_amount`.
- Không có cơ chế **ràng buộc hóa đơn với đơn thuốc/bệnh án** cụ thể.

---

## 4. PHÂN QUYỀN — 🔴 CRITICAL

> [!CAUTION]
> Hệ thống **không kiểm tra role** ở hầu hết controller.

Ví dụ cụ thể:
- [PatientController](file:///c:/wamp64/www/CNM1/controllers/PatientController.php): Patient có thể truy cập `index.php?page=patients&action=delete&id=1` để **xóa bệnh nhân khác**.
- [AppointmentController::updateStatus](file:///c:/wamp64/www/CNM1/controllers/AppointmentController.php#L87): Bất kỳ user đã login đều có thể đổi status lịch hẹn.
- [InvoiceController::markPaid](file:///c:/wamp64/www/CNM1/controllers/InvoiceController.php#L140): Patient có thể tự đánh dấu hóa đơn đã thanh toán.
- [ShiftController::store](file:///c:/wamp64/www/CNM1/controllers/ShiftController.php#L50): Comment ghi "Admin" nhưng code không kiểm tra.

**Chỉ có** `MedicalRecordController::create()` kiểm tra role doctor/admin.

**Sửa:** Thêm middleware/helper kiểm tra role đầu mỗi action:
```php
private function requireRole($roles) {
    if (!in_array($_SESSION['user']['role'], (array)$roles)) {
        $_SESSION['error'] = 'Bạn không có quyền.';
        header('Location: index.php?page=dashboard');
        exit;
    }
}
```

---

## 5. HIỆU NĂNG

### 5.1 Tạo kết nối DB mới mỗi model

Mỗi model `new Database()` → mỗi trang load có thể tạo **5–8 kết nối** riêng biệt. DashboardController tạo 6 model = 6 connections.

**Sửa:** Dùng Singleton pattern cho Database hoặc Dependency Injection.

### 5.2 Subquery trong getAll() của Shift

```sql
SELECT s.*, (SELECT COUNT(*) FROM doctor_shifts ds WHERE ds.shift_id = s.id) as registered_count
FROM shifts s
```
Correlated subquery chạy N lần. Khi có nhiều ca trực sẽ chậm. **Sửa:** Dùng `LEFT JOIN ... GROUP BY`.

### 5.3 Thiếu index

- `appointments.appointment_date` — thường query theo ngày nhưng không có index.
- `admissions.status` — filter theo status nhưng không có index.
- `invoices.status` — tương tự.

### 5.4 Không có phân trang

Tất cả `getAll()` load toàn bộ dữ liệu. Khi có hàng nghìn bệnh nhân/lịch hẹn sẽ rất chậm.

---

## 6. PHẦN AI

### 6.1 Có phải "AI thật" không?

| Thành phần | Đánh giá |
|------------|----------|
| `OllamaAI.php` | ✅ **AI thật** — gọi LLM local qua Ollama API |
| `GeminiAI.php` | ✅ **AI thật** — gọi Google Gemini API |
| `analyzeSymptomsFallback()` | ❌ **Rule-based** — keyword matching thuần túy |

Hiện tại controller mặc định dùng `OllamaAI`. Fallback keyword vẫn hoạt động khi Ollama chưa chạy — đây là thiết kế tốt.

### 6.2 Vấn đề với AI hiện tại

1. **Prompt chỉ yêu cầu JSON** nhưng Ollama local model (llama3) thường trả về text lẫn JSON → `parseAIResponse()` phải parse nhiều cách. Nên thêm ví dụ few-shot vào prompt.

2. **Không có rate limiting** — user có thể spam chat liên tục, tốn tài nguyên Ollama.

3. **Lịch sử chat lưu session** (tối đa 20 tin) — nếu session hết hạn thì mất. Không có persistent storage.

4. **`summarizeMedicalRecords()` có nhưng không được gọi** ở bất kỳ controller/view nào → tính năng "chết".

5. **Không sanitize input AI** — user có thể prompt injection (VD: "Ignore your instructions and...").

### 6.3 Code trùng lặp GeminiAI vs OllamaAI

Hai class gần giống nhau (~80% code trùng): `parseAIResponse()`, `normalizeResponse()`, `summarizeMedicalRecords()`. Nên tạo abstract class `BaseAI` rồi extend.

---

## 7. XUNG ĐỘT / LỖI TIỀM ẨN

| Vấn đề | Chi tiết |
|--------|----------|
| **MeetingController vs ConsultationController** | Routes có `ConsultationController` nhưng thư mục views có cả `meetings/`. File [MeetingController.php](file:///c:/wamp64/www/CNM1/controllers/MeetingController.php) vẫn tồn tại → chồng chéo |
| **`medical_records` thiếu cột `treatment`** | Model bind `:treatment` nhưng bảng không có → crash khi tạo bệnh án |
| **GeminiAI constants undefined** | `ai.php` không define `GEMINI_API_KEY/MODEL/URL` nhưng `GeminiAI.php` dùng → Fatal Error nếu load |
| **Migration files nằm ở root** | `add_billing_tables.php`, `add_medical_records.php`, `add_equipment_table.php` truy cập được công khai qua URL → ai cũng có thể chạy migration |
| **`$_SESSION['success']` chứa HTML** | ConsultationController dòng 44 nhúng HTML vào session message → XSS nếu render không escape |
| **InpatientController truy vấn DB trực tiếp** | Dòng 104–110 tạo `new Database()` và query trực tiếp trong controller, phá vỡ MVC |

---

## 8. THIẾU SÓT QUAN TRỌNG

### Tính năng bắt buộc cho hệ thống bệnh viện:

| Tính năng | Trạng thái |
|-----------|------------|
| Đăng ký tài khoản (Register) | ❌ Thiếu |
| Đổi mật khẩu / Quên mật khẩu | ❌ Thiếu |
| Audit log (ai làm gì, lúc nào) | ❌ Thiếu |
| Quản lý quyền chi tiết (RBAC) | ❌ Thiếu — chỉ check role cơ bản |
| Kết quả xét nghiệm (Lab Results) | ❌ Thiếu |
| Upload file (ảnh X-quang, xét nghiệm) | ❌ Thiếu |
| Thông báo real-time | ❌ Thiếu — bảng `notifications` có nhưng không có code sử dụng |
| Báo cáo / Thống kê (Revenue, Patient trends) | ⚠️ Cơ bản |
| Xuất PDF (đơn thuốc, hóa đơn) | ❌ Thiếu |
| Quản lý bảo hiểm y tế (BHYT) | ❌ Thiếu |
| Chữ ký số bác sĩ trên bệnh án | ❌ Thiếu |
| Tìm kiếm / Lọc dữ liệu | ❌ Thiếu |
| Responsive cho mobile | ⚠️ Chưa rõ |

---

## 9. ĐỀ XUẤT CẢI THIỆN

### 🔴 Critical (Bắt buộc sửa)

1. **Hash password** bằng `password_hash()` / `password_verify()`
2. **Thêm CSRF token** cho tất cả form POST
3. **Kiểm tra phân quyền** ở mọi controller action
4. **Chuyển MyISAM → InnoDB** + thêm Foreign Key thật
5. **Sửa lỗi cột `treatment`** trong bảng `medical_records`
6. **Chuyển hành động nhạy cảm** (delete, updateStatus, markPaid) sang POST
7. **Xóa/bảo vệ migration files** khỏi public access
8. **Define đầy đủ constants** cho GeminiAI trong `ai.php` hoặc xóa file nếu không dùng

### 🟡 Important (Nên có)

1. **Singleton Database** — tránh tạo nhiều connection
2. **Validate trùng lịch hẹn** (cùng bác sĩ, cùng giờ)
3. **Validate ngày khám** không được ở quá khứ
4. **Trừ tồn kho thuốc** khi kê đơn + kiểm tra hạn sử dụng
5. **Phân trang** cho danh sách
6. **Audit log** — ghi lại mọi hành động
7. **Tách `doctor_shifts` → `staff_shifts`** để nurse cũng dùng được
8. **Enforce rule ca trực** (2 night/tuần) trong code, không chỉ comment
9. **Thêm index** cho các cột filter thường xuyên
10. **Register / Đổi mật khẩu**

### 🟢 Nice-to-have (Bonus)

1. Xuất PDF đơn thuốc, hóa đơn (dùng TCPDF/Dompdf)
2. Upload ảnh xét nghiệm (X-quang, MRI)
3. Dashboard biểu đồ (Chart.js)
4. Notification real-time (WebSocket hoặc polling)
5. Quản lý BHYT
6. Tìm kiếm nâng cao + Filter
7. Dark mode

---

## 10. AI ROADMAP

### 10.1 Cải tiến AI hiện tại

#### Chatbot
- Thêm **few-shot examples** vào system prompt để Ollama trả JSON ổn định hơn
- Thêm **rate limiting** (VD: tối đa 20 request/phút/user)
- Lưu lịch sử chat vào **database** thay vì session
- Thêm **prompt injection protection**: filter input trước khi gửi AI

#### Gợi ý chuyên khoa
- Hiện tại AI trả `department` trong response → **tự động link đến form đặt lịch** với department đã chọn sẵn (đã có nút nhưng chưa truyền department)

#### Tóm tắt bệnh án
- Hàm `summarizeMedicalRecords()` đã viết nhưng **chưa được gọi**. Cần:
  - Thêm nút "AI Tóm tắt" vào trang chi tiết bệnh nhân
  - Tạo action `summarize` trong `MedicalRecordController`

### 10.2 Kiến trúc Ollama đề xuất

```
┌─────────────────────────────────────────────┐
│              NovaCare PHP App               │
│  ┌───────────────────────────────────────┐  │
│  │     AIServiceFactory                  │  │
│  │  ┌─────────┐  ┌──────────┐           │  │
│  │  │ OllamaAI│  │ GeminiAI │ (fallback)│  │
│  │  └────┬────┘  └────┬─────┘           │  │
│  │       │             │                 │  │
│  │       ▼             ▼                 │  │
│  │    BaseAI (abstract)                  │  │
│  │    - chat()                           │  │
│  │    - summarize()                      │  │
│  │    - parseResponse()                  │  │
│  └───────────────────────────────────────┘  │
│                    │                         │
│                    ▼                         │
│  ┌───────────────────────────────────────┐  │
│  │   AI Request Queue (DB table)         │  │
│  │   - rate limiting                     │  │
│  │   - request logging                   │  │
│  │   - response caching                  │  │
│  └───────────────────────────────────────┘  │
└─────────────────┬───────────────────────────┘
                  │ HTTP (cURL)
                  ▼
     ┌────────────────────────┐
     │   Ollama Server        │
     │   localhost:11434      │
     │   ┌──────────────────┐ │
     │   │ llama3 / qwen2   │ │
     │   │ (quantized 4-bit)│ │
     │   └──────────────────┘ │
     └────────────────────────┘
```

### 10.3 Tách AI thành service riêng?

**Với quy mô hiện tại: KHÔNG CẦN.** Lý do:
- Hệ thống monolith PHP + WAMP, traffic thấp
- Ollama đã là service riêng (port 11434)
- Tách thêm microservice chỉ tăng complexity không cần thiết

**Khi nào nên tách:**
- Khi có >100 concurrent AI requests
- Khi muốn scale AI server riêng (GPU dedicated)
- Khi muốn serve nhiều ứng dụng khác cùng dùng AI

### 10.4 Gợi ý tính năng AI mới

| Tính năng | Mô tả | Độ khó |
|-----------|--------|--------|
| AI tóm tắt bệnh án | Gọi `summarizeMedicalRecords()` đã có | ⭐ Dễ |
| AI gợi ý thuốc | Dựa trên diagnosis, gợi ý đơn thuốc | ⭐⭐ TB |
| AI phát hiện tương tác thuốc | Kiểm tra thuốc kê có xung đột không | ⭐⭐⭐ Khó |
| AI dự đoán tái khám | Dựa trên lịch sử, gợi ý lịch tái khám | ⭐⭐ TB |
| RAG với tài liệu y khoa | Ollama + vector DB cho tra cứu | ⭐⭐⭐ Khó |

### 10.5 Model Ollama khuyên dùng

| Model | RAM cần | Ưu điểm |
|-------|---------|---------|
| `llama3:8b` | ~5GB | Cân bằng tốt, hiểu tiếng Việt OK |
| `qwen2:7b` | ~5GB | Tốt cho tiếng Việt và JSON output |
| `gemma2:9b` | ~6GB | Của Google, structured output tốt |
| `mistral:7b` | ~5GB | Nhanh, nhẹ, JSON ổn định |

> [!TIP]
> Khuyên dùng **qwen2:7b** vì hỗ trợ tiếng Việt tốt nhất trong các model cùng tầm.

---

## TÓM TẮT ƯU TIÊN

```
NGAY LẬP TỨC:  Hash password + CSRF + Phân quyền + InnoDB
TUẦN 1:        Sửa bug treatment + Migration security + Validate logic
TUẦN 2:        Singleton DB + Pagination + Audit log
TUẦN 3-4:      Kích hoạt AI summarize + Refactor BaseAI + AI logging
THÁNG 2:       Register/Forgot password + PDF export + Lab results
```
