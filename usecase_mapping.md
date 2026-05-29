# Bản Đồ Usecase - Hệ Thống Bệnh Viện Thông Minh NovaCare 4.0

Dưới đây là danh sách phân rã chức năng chính theo từng vai trò (Actor) trong hệ thống để hỗ trợ vẽ sơ đồ Usecase.

---

## 👥 Các Actor & Usecase Chi Tiết

### 1. Bệnh Nhân (Patient) - Cổng Dịch Vụ Bệnh Nhân
* **Đăng ký & Đăng nhập:** Tạo tài khoản bệnh nhân, quản lý thông tin cá nhân & tiền sử bệnh án.
* **Đặt lịch hẹn:** Tìm kiếm bác sĩ/chuyên khoa và đặt lịch khám trực tuyến.
* **Xem hồ sơ sức khỏe:** Xem lịch sử bệnh án điện tử cá nhân (EMR), kết quả xét nghiệm, đơn thuốc được kê.
* **Thanh toán trực tuyến:** Xem hóa đơn viện phí và thực hiện thanh toán trực tuyến qua **VNPay Sandbox**.
* **Theo dõi hàng chờ:** Xem số thứ tự khám trực tuyến (Real-time Queue Display).

### 2. Lễ Tân / Tiếp Đón (Receptionist)
* **Tiếp nhận bệnh nhân:** Tìm kiếm hoặc tạo mới thông tin bệnh nhân khi đến viện.
* **Quản lý lịch hẹn:** Phê duyệt lịch hẹn trực tuyến của bệnh nhân, tiếp nhận lịch hẹn trực tiếp tại quầy.
* **Cấp số thứ tự:** Đưa bệnh nhân vào hàng đợi khám (Queue) của các phòng ban.
* **Quản lý phân ca:** Xem lịch trực của nhân viên y tế trong ca.

### 3. Bác Sĩ (Doctor)
* **Quản lý phòng khám:** Xem danh sách bệnh nhân đang đợi trong hàng chờ thời gian thực của phòng mình.
* **Khám bệnh & Chẩn đoán:**
  * Tra cứu hồ sơ bệnh án cũ của bệnh nhân.
  * Sử dụng **Trợ lý AI gợi ý chẩn đoán** & gợi ý phác đồ điều trị.
  * Chuẩn hóa chẩn đoán lâm sàng bằng cách tìm kiếm autocomplete mã bệnh **ICD-10**.
  * Ghi nhận sinh hiệu (Vitals) và nhận cảnh báo AI sinh hiệu (Vitals Alert).
* **Kê đơn thuốc:** Tạo đơn thuốc điện tử gửi thẳng qua hệ thống nhà thuốc.
* **Chỉ định cận lâm sàng:** Yêu cầu xét nghiệm (Lab Orders), chụp chiếu cận lâm sàng.
* **Chỉ định nội trú:** Chuyển bệnh nhân nhập viện điều trị nội trú.

### 4. Điều Dưỡng (Nurse)
* **Tiếp nhận nội trú:** Đón bệnh nhân vào khoa nội trú, phân giường bệnh.
* **Theo dõi & Chăm sóc:**
  * Ghi phiếu chăm sóc, cập nhật chỉ số sinh hiệu (Vitals) hàng ngày.
  * Nhận cảnh báo thông minh AI khi sinh hiệu bệnh nhân rơi vào trạng thái nguy kịch (Danger/Critical).
* **Quản lý buồng/giường:** Quản lý danh sách giường trống, điều chuyển phòng bệnh.
* **Phát thuốc nội trú:** Theo dõi và thực hiện y lệnh thuốc của bác sĩ.

### 5. Kỹ Thuật Viên Xét Nghiệm (Technician)
* **Tiếp nhận chỉ định:** Xem danh sách các yêu cầu xét nghiệm từ bác sĩ phòng khám/nội trú.
* **Thực hiện xét nghiệm:** Ghi nhận mẫu thử và cập nhật kết quả xét nghiệm (Lab Results) lên hệ thống.
* **Quản lý thiết bị:** Kết nối và theo dõi trạng thái hoạt động của các thiết bị y tế IoT được phân công.

### 6. Dược Sĩ (Pharmacist)
* **Quản lý đơn thuốc:** Tiếp nhận đơn thuốc điện tử từ phòng khám của bác sĩ.
* **Duyệt & Cấp phát thuốc:**
  * Kiểm tra tình trạng thanh toán hóa đơn của đơn thuốc.
  * Duyệt đơn và thực hiện cấp phát thuốc cho bệnh nhân.
* **Quản lý kho dược:** Theo dõi số lượng tồn kho thuốc, cảnh báo thuốc sắp hết hạn (Expired warning) hoặc hết hàng, cập nhật nhập/xuất kho.

### 7. Thu Ngân (Cashier)
* **Quản lý hóa đơn:** Tạo hóa đơn viện phí từ đơn thuốc, dịch vụ kỹ thuật cận lâm sàng, tiền giường bệnh.
* **Thu phí tại quầy:** Xác nhận thanh toán trực tiếp tại quầy bằng các hình thức: tiền mặt, quẹt thẻ, chuyển khoản, ví MoMo, VNPay tại quầy.
* **Áp dụng BHYT:** Kiểm tra thẻ bảo hiểm y tế và tính trừ khấu hao chi trả của BHYT tự động trước khi xuất hóa đơn.

### 8. Giám Đốc (Director)
* **Xem Dashboard vận hành 4.0:**
  * Theo dõi doanh thu thực tế, lượng bệnh nhân đến khám, công suất giường bệnh hiện tại.
  * Chạy **AI Dự báo tải & vận hành bệnh viện** trong 7 ngày tiếp theo để điều phối nhân sự.
* **Xem báo cáo:** Xem thống kê doanh thu khoa phòng, báo cáo xuất nhập tồn kho dược.

### 9. Quản Trị Viên (Admin)
* **Quản lý người dùng:** Thêm, sửa, xóa, khóa tài khoản nhân sự và phân quyền vai trò (Role-based access) - Chi tiết tại [Tài liệu đặc tả Quản lý người dùng](file:///d:/wamp64/www/NovaCare/user_management_specification.md).
* **Quản lý danh mục:** Quản lý danh mục thuốc, dịch vụ khám, thiết bị y tế, danh mục khoa phòng.
* **Theo dõi nhật ký hệ thống:** Xem chi tiết nhật ký thao tác (Audit Logs) của mọi nhân sự để kiểm toán bảo mật.
