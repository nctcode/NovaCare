# API Contract: REST API v1 - NovaCare

Tài liệu đặc tả chi tiết giao diện lập trình (API Contract) dành cho Patient Mobile App kết nối tới NovaCare Backend.

---

## 📌 Quy định chung
* **Base URL mặc định (Android Emulator):** `http://10.0.2.2/NovaCare/api/v1`
* **Base URL mặc định (Thiết bị thật / LAN):** `http://<IP_MÁY_TÍNH>/NovaCare/api/v1`
* **Base URL mặc định (Postman / Browser):** `http://localhost/NovaCare/api/v1`
* **Định dạng dữ liệu:** JSON (`application/json`)
* **Xác thực:** Gắn header `Authorization: Bearer <access_token>` cho các endpoint yêu cầu xác thực.

---

## 🔐 1. Authentication & Profile

### 1.1 Đăng ký tài khoản (`POST /auth/register`)
* **Mô tả:** Đăng ký tài khoản bệnh nhân mới.
* **Yêu cầu xác thực:** Không
* **Request Body:**
```json
{
  "name": "Nguyen Van A",
  "email": "nguyenvana@gmail.com",
  "phone": "0912345678",
  "password": "password123",
  "confirm_password": "password123"
}
```
* **Response Body (201 Created):**
```json
{
  "success": true,
  "message": "Đăng ký tài khoản thành công và tạo hồ sơ bệnh nhân.",
  "data": {
    "access_token": "eyJ0eXAi...",
    "refresh_token": "ab12cd34...",
    "user": {
      "id": 12,
      "name": "Nguyen Van A",
      "email": "nguyenvana@gmail.com",
      "phone": "0912345678",
      "role": "patient",
      "status": "active"
    }
  },
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `422 Unprocessable Entity`: Validation lỗi (email không đúng định dạng, phone không đủ chữ số, confirm_password không khớp).
  * `409 Conflict`: Email hoặc Số điện thoại đã được đăng ký.

### 1.2 Đăng nhập (`POST /auth/login`)
* **Mô tả:** Đăng nhập lấy cặp Token (Access Token và Refresh Token).
* **Yêu cầu xác thực:** Không
* **Request Body:**
```json
{
  "email": "nguyenvana@gmail.com",
  "password": "password123"
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "access_token": "eyJ0eXAi...",
    "refresh_token": "ab12cd34...",
    "user": {
      "id": 12,
      "name": "Nguyen Van A",
      "email": "nguyenvana@gmail.com",
      "phone": "0912345678",
      "role": "patient",
      "status": "active"
    }
  },
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `401 Unauthorized`: Sai mật khẩu hoặc tài khoản đã bị khóa.
  * `404 Not Found`: Không tìm thấy email đăng ký.
  * `422 Unprocessable Entity`: Thiếu email hoặc mật khẩu.

### 1.3 Làm mới Token (`POST /auth/refresh`)
* **Mô tả:** Dùng refresh token để nhận access token mới khi hết hạn.
* **Yêu cầu xác thực:** Không
* **Request Body:**
```json
{
  "refresh_token": "ab12cd34..."
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Làm mới token thành công.",
  "data": {
    "access_token": "eyJ0eXAi...",
    "refresh_token": "cd56ef78..."
  },
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `401 Unauthorized`: Refresh token không hợp lệ, đã bị hủy (revoked) hoặc hết hạn.

### 1.4 Đăng xuất (`POST /auth/logout`)
* **Mô tả:** Hủy refresh token hiện tại trong database.
* **Yêu cầu xác thực:** Có
* **Request Body:**
```json
{
  "refresh_token": "cd56ef78..."
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Đăng xuất thành công.",
  "data": null,
  "errors": null,
  "meta": null
}
```

### 1.5 Lấy thông tin user hiện tại (`GET /me`)
* **Mô tả:** Lấy thông tin tài khoản đang login từ access token.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy thông tin tài khoản thành công",
  "data": {
    "user_id": 12,
    "name": "Nguyen Van A",
    "email": "nguyenvana@gmail.com",
    "phone": "0912345678",
    "role": "patient"
  },
  "errors": null,
  "meta": null
}
```

### 1.6 Xem hồ sơ bệnh nhân (`GET /patient/profile`)
* **Mô tả:** Lấy hồ sơ chi tiết của bệnh nhân liên kết với user.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy thông tin hồ sơ bệnh nhân thành công.",
  "data": {
    "id": 5,
    "user_id": 12,
    "name": "Nguyen Van A",
    "email": "nguyenvana@gmail.com",
    "phone": "0912345678",
    "date_of_birth": "1998-05-15",
    "gender": "male",
    "address": "123 Đường ABC, Hà Nội",
    "blood_type": "O",
    "medical_history": "Dị ứng với penicillin.",
    "emergency_contact": "Nguyen Van B - 0987654321",
    "insurance_number": "GD4010123456789"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* Các trường `date_of_birth`, `gender`, `address`, `blood_type`, `medical_history`, `emergency_contact`, `insurance_number` có thể mang giá trị `null` nếu bệnh nhân chưa cập nhật.

### 1.7 Cập nhật hồ sơ bệnh nhân (`PUT /patient/profile`)
* **Mô tả:** Thay đổi thông tin cá nhân của bệnh nhân.
* **Yêu cầu xác thực:** Có
* **Request Body:**
```json
{
  "name": "Nguyen Van A",
  "phone": "0912345678",
  "date_of_birth": "1998-05-15",
  "gender": "male",
  "address": "456 Đường XYZ, Hà Nội",
  "blood_type": "O",
  "emergency_contact": "Nguyen Van B - 0987654321",
  "insurance_number": "GD4010123456789"
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Cập nhật hồ sơ bệnh nhân thành công.",
  "data": {
    "id": 5,
    "user_id": 12,
    "name": "Nguyen Van A",
    "email": "nguyenvana@gmail.com",
    "phone": "0912345678",
    "date_of_birth": "1998-05-15",
    "gender": "male",
    "address": "456 Đường XYZ, Hà Nội",
    "blood_type": "O",
    "medical_history": "Dị ứng với penicillin.",
    "emergency_contact": "Nguyen Van B - 0987654321",
    "insurance_number": "GD4010123456789"
  },
  "errors": null,
  "meta": null
}
```

---

## 🏥 2. Departments & Doctors

### 2.1 Danh sách khoa phòng (`GET /departments`)
* **Mô tả:** Lấy danh sách toàn bộ khoa phòng đang hoạt động.
* **Yêu cầu xác thực:** Không (Public)
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách khoa phòng thành công",
  "data": [
    {
      "id": 3,
      "name": "Khoa Tim Mạch",
      "description": "Chuyên khám và điều trị các bệnh lý tim mạch, huyết áp.",
      "status": "active"
    }
  ],
  "errors": null,
  "meta": null
}
```

### 2.2 Danh sách bác sĩ (`GET /doctors`)
* **Mô tả:** Lấy danh sách bác sĩ, hỗ trợ lọc theo khoa phòng và từ khóa tìm kiếm.
* **Yêu cầu xác thực:** Không (Public)
* **Query Parameters:**
  * `department_id` (INT, optional) - Lọc theo ID khoa.
  * `keyword` (STRING, optional) - Tìm kiếm theo tên bác sĩ hoặc chuyên khoa.
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách bác sĩ thành công",
  "data": [
    {
      "id": 1,
      "name": "Bác sĩ Tim Mạch A",
      "email": "bstimmacha@novacare.com",
      "phone": "0900000001",
      "specialty": "Tim mạch can thiệp",
      "experience_years": 8,
      "status": "active",
      "department_ids": [3],
      "avatar": null
    }
  ],
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `avatar` có thể mang giá trị `null`.

---

## 📅 3. Appointments & Booking

### 3.1 Slot lịch khám khả dụng (`GET /appointments/available-slots`)
* **Mô tả:** Tra cứu các giờ khám (cách nhau 30 phút) còn trống của bác sĩ.
* **Yêu cầu xác thực:** Có
* **Query Parameters:**
  * `doctor_id` (INT, required)
  * `date` (STRING, required) - Định dạng `YYYY-MM-DD`.
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy slot lịch khả dụng thành công",
  "data": [
    "08:00",
    "08:30",
    "09:00",
    "09:30",
    "10:00",
    "10:30",
    "11:00",
    "11:30"
  ],
  "errors": null,
  "meta": null
}
```

### 3.2 Đặt lịch hẹn khám (`POST /appointments`)
* **Mô tả:** Tạo mới lịch hẹn khám bệnh.
* **Yêu cầu xác thực:** Có
* **Cảnh báo quan trọng:** Không gửi trường `type` (hệ thống chưa hỗ trợ).
* **Request Body:**
```json
{
  "doctor_id": 1,
  "department_id": 3,
  "appointment_date": "2026-07-06",
  "appointment_time": "08:00",
  "reason": "Khám tim định kỳ"
}
```
* **Response Body (201 Created):**
```json
{
  "success": true,
  "message": "Đặt lịch hẹn khám bệnh thành công",
  "data": {
    "id": 20,
    "patient_id": 5,
    "doctor_id": 1,
    "appointment_date": "2026-07-06 08:00:00",
    "reason": "Khám tim định kỳ",
    "status": "pending",
    "created_at": "2026-07-04 23:55:00"
  },
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `409 Conflict`: Bác sĩ đã bị đặt lịch trùng giờ này, hoặc bản thân bệnh nhân đã có một lịch hẹn khác hoạt động cùng giờ.
  * `422 Unprocessable Entity`: Định dạng ngày giờ sai, ngày hẹn ở quá khứ, hoặc gửi kèm trường `type`.

### 3.3 Danh sách lịch hẹn của tôi (`GET /appointments/my`)
* **Mô tả:** Lấy danh sách lịch hẹn của bệnh nhân hiện tại (có phân trang).
* **Yêu cầu xác thực:** Có
* **Query Parameters:**
  * `status` (STRING, optional) - Lọc trạng thái (`pending`, `confirmed`, `completed`, `cancelled`).
  * `from_date` (STRING, optional) - Lọc từ ngày (`YYYY-MM-DD`).
  * `to_date` (STRING, optional) - Lọc đến ngày (`YYYY-MM-DD`).
  * `page` (INT, optional) - Trang hiện tại (mặc định: 1).
  * `limit` (INT, optional) - Số lượng/trang (mặc định: 10).
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách lịch hẹn thành công",
  "data": [
    {
      "id": 20,
      "appointment_date": "2026-07-06 08:00:00",
      "reason": "Khám tim định kỳ",
      "status": "pending",
      "doctor_name": "Bác sĩ Tim Mạch A",
      "department_name": "Khoa Tim Mạch"
    }
  ],
  "errors": null,
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

### 3.4 Chi tiết lịch hẹn (`GET /appointments/{id}`)
* **Mô tả:** Xem chi tiết một lịch hẹn cụ thể.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy thông tin lịch hẹn thành công",
  "data": {
    "id": 20,
    "patient_id": 5,
    "doctor_id": 1,
    "appointment_date": "2026-07-06 08:00:00",
    "reason": "Khám tim định kỳ",
    "status": "pending",
    "doctor_name": "Bác sĩ Tim Mạch A",
    "department_name": "Khoa Tim Mạch",
    "room_name": "Phòng khám 302"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `room_name` có thể mang giá trị `null` nếu lịch hẹn chưa được xác nhận hoặc chưa xếp phòng.

### 3.5 Hủy lịch hẹn (`POST /appointments/{id}/cancel`)
* **Mô tả:** Bệnh nhân tự hủy lịch hẹn (chỉ được hủy khi trạng thái là `pending` hoặc `confirmed`, và giờ khám chưa qua).
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Hủy lịch hẹn thành công",
  "data": {
    "id": 20,
    "status": "cancelled",
    "appointment_date": "2026-07-06 08:00:00"
  },
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `400 Bad Request`: Trạng thái lịch không được phép hủy (đã khám, đã hủy từ trước), hoặc lịch hẹn ở quá khứ.
  * `403 Forbidden`: Người dùng cố gắng hủy lịch của bệnh nhân khác (IDOR).
  * `404 Not Found`: Lịch hẹn không tồn tại.

---

## 🚶‍♂️ 4. Queue Tracking

### 4.1 Tra cứu số thứ tự hôm nay (`GET /queue/my-ticket`)
* **Mô tả:** Lấy thông tin số thứ tự khám hôm nay của bệnh nhân.
* **Yêu cầu xác thực:** Có
* **Trường hợp 1: Không có lượt khám hôm nay (200 OK):**
```json
{
  "success": true,
  "message": "Bệnh nhân chưa có số thứ tự khám hôm nay.",
  "data": null,
  "errors": null,
  "meta": null
}
```
* **Trường hợp 2: Có lượt khám hôm nay (200 OK):**
```json
{
  "success": true,
  "message": "Lấy thông tin số thứ tự khám hiện tại thành công.",
  "data": {
    "id": 15,
    "ticket_number": "A102",
    "current_calling_number": "A097",
    "estimated_waiting_count": 5,
    "department_name": "Khoa Tim Mạch",
    "doctor_name": "Bác sĩ Tim Mạch A",
    "room_name": "Phòng khám 302",
    "status": "waiting"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `current_calling_number` có thể mang giá trị `null` nếu phòng khám chưa gọi số nào hôm nay. `status` gồm: `'waiting'`, `'calling'`, `'completed'`, `'cancelled'`.

---

## 📂 5. Medical Records & Prescriptions

### 5.1 Danh sách bệnh án (`GET /records/my`)
* **Mô tả:** Lấy lịch sử bệnh án cá nhân.
* **Yêu cầu xác thực:** Có
* **Query Parameters:** `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách bệnh án thành công",
  "data": [
    {
      "id": 8,
      "record_date": "2026-06-15",
      "diagnosis": "Tăng huyết áp vô căn",
      "treatment_plan": "Uống thuốc đều đặn và giảm ăn mặn.",
      "doctor_name": "Bác sĩ Tim Mạch A"
    }
  ],
  "errors": null,
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

### 5.2 Chi tiết bệnh án (`GET /records/{id}`)
* **Mô tả:** Xem chi tiết một bệnh án bao gồm các chỉ số sinh hiệu.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy chi tiết bệnh án thành công",
  "data": {
    "id": 8,
    "record_date": "2026-06-15",
    "diagnosis": "Tăng huyết áp vô căn",
    "symptoms": "Đau đầu nhẹ, hoa mắt chóng mặt.",
    "treatment_plan": "Uống thuốc đều đặn và giảm ăn mặn.",
    "doctor_name": "Bác sĩ Tim Mạch A",
    "department_name": "Khoa Tim Mạch",
    "bp": "140/90 mmHg",
    "temperature": "36.8 °C",
    "weight": "68 kg",
    "height": "172 cm"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* Chỉ số sinh hiệu `bp`, `temperature`, `weight`, `height` có thể mang giá trị `null`.

### 5.3 Danh sách đơn thuốc (`GET /prescriptions/my`)
* **Mô tả:** Lấy lịch sử đơn thuốc cá nhân.
* **Yêu cầu xác thực:** Có
* **Query Parameters:** `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách đơn thuốc thành công",
  "data": [
    {
      "id": 8,
      "prescription_date": "2026-06-15",
      "notes": "Uống thuốc sau khi ăn.",
      "doctor_name": "Bác sĩ Tim Mạch A",
      "record_id": 8
    }
  ],
  "errors": null,
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

### 5.4 Chi tiết đơn thuốc (`GET /prescriptions/{id}`)
* **Mô tả:** Xem chi tiết một đơn thuốc và danh sách biệt dược đi kèm.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy chi tiết đơn thuốc thành công",
  "data": {
    "id": 8,
    "prescription_date": "2026-06-15",
    "notes": "Uống thuốc sau khi ăn.",
    "doctor_name": "Bác sĩ Tim Mạch A",
    "patient_name": "Nguyen Van A",
    "items": [
      {
        "id": 14,
        "medicine_name": "Amlodipine 5mg",
        "dosage": "1 viên",
        "duration": "30 ngày",
        "frequency": "1 lần/ngày",
        "instructions": "Uống sáng sau ăn.",
        "quantity": 30
      }
    ]
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `frequency` có thể `null`.

---

## 💳 6. Invoices & Payments

### 6.1 Danh sách hóa đơn (`GET /invoices/my`)
* **Mô tả:** Lấy danh sách hóa đơn viện phí cá nhân.
* **Yêu cầu xác thực:** Có
* **Query Parameters:**
  * `status` (STRING, optional) - Lọc trạng thái (`pending`, `paid`, `cancelled`).
  * `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách hóa đơn thành công",
  "data": [
    {
      "id": 18,
      "invoice_code": "INV-000018",
      "created_at": "2026-07-04 23:55:00",
      "total_amount": 150000.00,
      "discount": 0.00,
      "insurance_discount": 0.00,
      "final_amount": 150000.00,
      "payment_status": "pending"
    }
  ],
  "errors": null,
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

### 6.2 Chi tiết hóa đơn (`GET /invoices/{id}`)
* **Mô tả:** Xem chi tiết một hóa đơn cụ thể.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy chi tiết hóa đơn thành công",
  "data": {
    "id": 18,
    "invoice_code": "INV-000018",
    "created_at": "2026-07-04 23:55:00",
    "total_amount": 150000.00,
    "discount": 0.00,
    "insurance_coverage": 0.00,
    "final_amount": 150000.00,
    "status": "pending",
    "payment_method": null
  },
  "errors": null,
  "meta": null
}
```

### 6.3 Tạo link thanh toán VNPay (`POST /payments/vnpay/create`)
* **Mô tả:** Khởi tạo giao dịch thanh toán và tạo liên kết dẫn tới cổng VNPay Sandbox.
* **Yêu cầu xác thực:** Có
* **Request Body:**
```json
{
  "invoice_id": 18,
  "return_url": "http://10.0.2.2/NovaCare/api/v1/payments/vnpay/return"
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Tạo liên kết thanh toán VNPay thành công",
  "data": {
    "payment_url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=...",
    "invoice_id": 18,
    "amount": 150000,
    "provider": "vnpay",
    "transaction_ref": "18_1783172111"
  },
  "errors": null,
  "meta": null
}
```

### 6.4 Tra cứu trạng thái thanh toán (`GET /payments/{invoice_id}/status`)
* **Mô tả:** Tra cứu trạng thái cập nhật thanh toán thực tế của hóa đơn.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy trạng thái thanh toán thành công",
  "data": {
    "invoice_id": 18,
    "payment_status": "paid",
    "payment_method": "vnpay",
    "total_amount": 150000,
    "final_amount": 150000,
    "paid_at": "2026-07-04 23:58:12",
    "transaction_ref": "18_1783172111"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `paid_at` và `transaction_ref` mang giá trị `null` nếu trạng thái là `pending`.

### 6.5 Lịch sử giao dịch thanh toán (`GET /payments/history`)
* **Mô tả:** Xem danh sách nỗ lực thanh toán cá nhân.
* **Yêu cầu xác thực:** Có
* **Query Parameters:** `status`, `provider`, `from_date`, `to_date`, `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy lịch sử thanh toán thành công",
  "data": [
    {
      "id": 3,
      "invoice_id": 18,
      "provider": "vnpay",
      "transaction_ref": "18_1783172111",
      "amount": 150000.00,
      "status": "success",
      "paid_at": "2026-07-04 23:58:12",
      "created_at": "2026-07-04 23:55:02"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```

### 6.6 Tạo thanh toán MoMo (`POST /payments/momo/create`)
* **Mô tả:** API giả lập chưa cấu hình phục vụ MoMo.
* **Yêu cầu xác thực:** Có
* **Response Body (501 Not Implemented):**
```json
{
  "success": false,
  "message": "MoMo chưa được cấu hình trong Phase 2B.",
  "data": null,
  "errors": {},
  "meta": {}
}
```

---

## 🤖 7. AI Chat Assistant

### 7.1 Gửi tin nhắn chat triệu chứng (`POST /ai/chat`)
* **Mô tả:** Gửi triệu chứng bệnh lý và nhận phản hồi dự đoán/khoa phòng gợi ý từ AI.
* **Yêu cầu xác thực:** Có
* **Request Body:**
```json
{
  "message": "Tôi bị đau ngực nhẹ khi chạy bộ."
}
```
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Gửi và phản hồi AI thành công",
  "data": {
    "reply": "Dựa trên triệu chứng của bạn, đây có thể là dấu hiệu đau thắt ngực do thiếu máu cơ tim cục bộ...",
    "disclaimer": "AI chỉ hỗ trợ tham khảo triệu chứng, không thay thế chỉ định và chẩn đoán chính thức từ bác sĩ chuyên khoa.",
    "urgency_level": "medium",
    "department_suggestion": "Khoa Tim Mạch",
    "created_at": "2026-07-04 23:59:15"
  },
  "errors": null,
  "meta": null
}
```
*Lưu ý:* `urgency_level` gồm: `'low'`, `'medium'`, `'high'`.

### 7.2 Lịch sử trò chuyện AI (`GET /ai/chat-history`)
* **Mô tả:** Lấy danh sách cuộc trò chuyện cũ (sắp xếp tăng dần theo thời gian).
* **Yêu cầu xác thực:** Có
* **Query Parameters:** `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy lịch sử trò chuyện AI thành công",
  "data": [
    {
      "id": 1,
      "sender": "user",
      "message": "Tôi bị đau ngực nhẹ khi chạy bộ.",
      "urgency_level": null,
      "department_suggestion": null,
      "created_at": "2026-07-04 23:59:00"
    },
    {
      "id": 2,
      "sender": "assistant",
      "message": "Dựa trên triệu chứng của bạn, đây có thể là...",
      "urgency_level": "medium",
      "department_suggestion": "Khoa Tim Mạch",
      "created_at": "2026-07-04 23:59:15"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 2,
    "total_pages": 1
  }
}
```

### 7.3 Xóa lịch sử trò chuyện AI (`DELETE /ai/chat-history`)
* **Mô tả:** Xóa mềm (soft delete) toàn bộ lịch sử trò chuyện AI của bệnh nhân.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Xóa lịch sử trò chuyện AI thành công.",
  "data": null,
  "errors": null,
  "meta": null
}
```

---

## 🔔 8. Notifications

### 8.1 Danh sách thông báo (`GET /notifications`)
* **Mô tả:** Lấy toàn bộ thông báo của bệnh nhân hiện tại.
* **Yêu cầu xác thực:** Có
* **Query Parameters:**
  * `is_read` (BOOLEAN, optional) - Lọc đã đọc (`true` hoặc `1`) hoặc chưa đọc (`false` hoặc `0`).
  * `page`, `limit`
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy danh sách thông báo thành công",
  "data": [
    {
      "id": 22,
      "title": "Đặt lịch khám thành công",
      "message": "Lịch hẹn khám bác sĩ Tim Mạch A vào ngày 2026-07-06 đã được đặt thành công.",
      "type": "appointment",
      "is_read": false,
      "created_at": "2026-07-04 23:55:00"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "total_pages": 1
  }
}
```
*Lưu ý:* `type` có các giá trị: `'appointment'`, `'payment'`, `'ai_alert'`, `'general'`.

### 8.2 Số lượng thông báo chưa đọc (`GET /notifications/unread-count`)
* **Mô tả:** Đếm số thông báo có trạng thái chưa đọc.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Lấy số lượng thông báo chưa đọc thành công",
  "data": {
    "unread_count": 1
  },
  "errors": null,
  "meta": null
}
```

### 8.3 Đánh dấu thông báo đã đọc (`POST /notifications/{id}/read`)
* **Mô tả:** Thay đổi trạng thái thông báo thành đã đọc.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Đã đánh dấu thông báo là đã đọc.",
  "data": null,
  "errors": null,
  "meta": null
}
```
* **Mã lỗi thường gặp:**
  * `403 Forbidden`: Người dùng cố tình đọc thông báo của bệnh nhân khác (IDOR).
  * `404 Not Found`: Thông báo không tồn tại.

### 8.4 Đánh dấu đã đọc tất cả (`POST /notifications/read-all`)
* **Mô tả:** Đổi toàn bộ trạng thái thông báo chưa đọc thành đã đọc của chính bệnh nhân đang đăng nhập.
* **Yêu cầu xác thực:** Có
* **Response Body (200 OK):**
```json
{
  "success": true,
  "message": "Đã đánh dấu tất cả thông báo của bạn là đã đọc.",
  "data": null,
  "errors": null,
  "meta": null
}
```
