<?php
/**
 * ApiAppointmentController - Quản lý lịch hẹn khám bệnh cho API v1
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../core/RequestValidator.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Doctor.php';
require_once __DIR__ . '/../../models/Department.php';
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../models/Notification.php';
require_once __DIR__ . '/../../helpers/AuditLog.php';

class ApiAppointmentController extends BaseApiController {

    private $patientModel;
    private $doctorModel;
    private $departmentModel;
    private $appointmentModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->departmentModel = new Department();
        $this->appointmentModel = new Appointment();
    }

    /**
     * GET /api/v1/appointments/available-slots
     */
    public function availableSlots() {
        $doctorId = $_GET['doctor_id'] ?? null;
        $deptId = $_GET['department_id'] ?? null;
        $date = $_GET['date'] ?? null;

        if (!$doctorId || !$date) {
            return $this->sendError('Thiếu tham số doctor_id hoặc date.', 400);
        }

        // Validate định dạng ngày YYYY-MM-DD
        $validator = new RequestValidator();
        $validator->date($date, 'date');
        if ($validator->hasErrors()) {
            return $this->sendValidationError($validator->getErrors());
        }

        // Kiểm tra ngày không nằm trong quá khứ
        if (strtotime($date . ' 23:59:59') < time()) {
            return $this->sendError('Không thể tra cứu slot lịch ở ngày trong quá khứ.', 422);
        }

        // Kiểm tra bác sĩ có tồn tại không
        $doctor = $this->doctorModel->findById($doctorId);
        if (!$doctor) {
            return $this->sendNotFound('Không tìm thấy bác sĩ yêu cầu.');
        }

        // Kiểm tra quan hệ khoa nếu có gửi department_id
        if ($deptId) {
            $department = $this->departmentModel->findById($deptId);
            if (!$department) {
                return $this->sendNotFound('Không tìm thấy khoa yêu cầu.');
            }
            // Kiểm tra bác sĩ có thuộc khoa này không
            if (!empty($doctor['department_ids']) && !in_array($deptId, $doctor['department_ids'])) {
                return $this->sendError('Bác sĩ không thuộc khoa được chọn.', 400);
            }
        }

        $db = new Database();
        $conn = $db->getConnection();

        // 1. Tìm ca trực thực tế từ bảng shifts & doctor_shifts
        $sqlShift = "SELECT s.* FROM doctor_shifts ds 
                     JOIN shifts s ON ds.shift_id = s.id 
                     WHERE ds.doctor_id = :doctor_id 
                       AND s.shift_date = :date 
                       AND ds.status IN ('approved', 'assigned')
                     ORDER BY s.start_time ASC";
        
        $stmtShift = $conn->prepare($sqlShift);
        $stmtShift->execute([
            ':doctor_id' => $doctorId,
            ':date'      => $date
        ]);
        $shifts = $stmtShift->fetchAll(PDO::FETCH_ASSOC);

        $slots = [];

        if (!empty($shifts)) {
            // Sinh slots từ ca trực thực tế
            foreach ($shifts as $shift) {
                $start = strtotime($shift['start_time']);
                $end = strtotime($shift['end_time']);
                // Mỗi ca khám cách nhau 30 phút
                for ($time = $start; $time < $end; $time += 1800) {
                    $slots[] = date('H:i', $time);
                }
            }
        } else {
            // MVP fallback: Sinh slots theo giờ hành chính mặc định của bệnh viện
            // Sáng: 08:00 - 12:00, Chiều: 13:30 - 17:00
            $morningStart = strtotime('08:00');
            $morningEnd = strtotime('12:00');
            for ($time = $morningStart; $time < $morningEnd; $time += 1800) {
                $slots[] = date('H:i', $time);
            }

            $afternoonStart = strtotime('13:30');
            $afternoonEnd = strtotime('17:00');
            for ($time = $afternoonStart; $time < $afternoonEnd; $time += 1800) {
                $slots[] = date('H:i', $time);
            }
        }

        // Lọc bỏ các slot trong ngày hiện tại mà thời gian đã trôi qua
        if ($date === date('Y-m-d')) {
            $currentTime = date('H:i');
            $slots = array_filter($slots, function($s) use ($currentTime) {
                return $s > $currentTime;
            });
        }

        // 2. Loại bỏ các slot đã bị đặt lịch
        $sqlBooked = "SELECT appointment_date FROM appointments 
                      WHERE doctor_id = :doctor_id 
                        AND DATE(appointment_date) = :date 
                        AND status NOT IN ('cancelled') 
                        AND deleted_at IS NULL";
        $stmtBooked = $conn->prepare($sqlBooked);
        $stmtBooked->execute([
            ':doctor_id' => $doctorId,
            ':date'      => $date
        ]);
        $bookedAppts = $stmtBooked->fetchAll(PDO::FETCH_ASSOC);

        $availableSlots = [];
        foreach ($slots as $slot) {
            $slotTime = strtotime("$date $slot:00");
            $isBooked = false;
            foreach ($bookedAppts as $appt) {
                $apptTime = strtotime($appt['appointment_date']);
                // Cách nhau dưới 30 phút được coi là trùng
                if (abs($slotTime - $apptTime) < 1800) {
                    $isBooked = true;
                    break;
                }
            }
            if (!$isBooked) {
                $availableSlots[] = $slot;
            }
        }

        return $this->sendSuccess(array_values($availableSlots), 'Lấy slot lịch khả dụng thành công');
    }

    /**
     * POST /api/v1/appointments
     */
    public function create() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $input = $this->getJsonInput();

        // 1. Chặn trường 'type' không được hỗ trợ trong Phase 2A để tránh hiểu nhầm
        if (isset($input['type'])) {
            return $this->sendError('Field type chưa được hỗ trợ trong Phase 2A', 422);
        }

        // Validate các trường bắt buộc
        $validator = new RequestValidator();
        $validator->required($input, ['doctor_id', 'department_id', 'appointment_date', 'appointment_time', 'reason'])
                  ->date($input['appointment_date'] ?? '', 'appointment_date');
        
        if ($validator->hasErrors()) {
            return $this->sendValidationError($validator->getErrors());
        }

        $doctorId = (int)$input['doctor_id'];
        $deptId = (int)$input['department_id'];
        $appDate = trim($input['appointment_date']); // YYYY-MM-DD
        $appTime = trim($input['appointment_time']); // HH:mm
        $reason = trim($input['reason']);

        // Validate định dạng giờ HH:mm
        if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $appTime)) {
            return $this->sendError('Định dạng thời gian phải là HH:mm.', 422);
        }

        $dateTimeStr = "$appDate $appTime:00";
        $appDateTime = strtotime($dateTimeStr);

        // Kiểm tra xem lịch hẹn có ở trong quá khứ không
        if ($appDateTime < time()) {
            return $this->sendError('Thời gian hẹn không được ở trong quá khứ.', 422);
        }

        // Lấy patient_id của người dùng hiện tại
        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin bệnh nhân tương ứng với tài khoản của bạn.');
        }
        $patientId = $patient['id'];

        // Kiểm tra bác sĩ có tồn tại không
        $doctor = $this->doctorModel->findById($doctorId);
        if (!$doctor) {
            return $this->sendNotFound('Không tìm thấy bác sĩ yêu cầu.');
        }

        // Kiểm tra quan hệ khoa
        if (!empty($doctor['department_ids']) && !in_array($deptId, $doctor['department_ids'])) {
            return $this->sendError('Bác sĩ không thuộc khoa được chọn.', 400);
        }

        // Tiến hành ghi dữ liệu sử dụng Transaction và Khóa đọc tránh Race Condition
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // Khóa đọc tất cả lịch hẹn ngày đó của bác sĩ để đảm bảo an toàn tuyệt đối
            $sqlLock = "SELECT id, appointment_date FROM appointments 
                        WHERE doctor_id = :doctor_id 
                          AND DATE(appointment_date) = :date 
                          AND status NOT IN ('cancelled') 
                          AND deleted_at IS NULL 
                        FOR UPDATE";
            $stmtLock = $conn->prepare($sqlLock);
            $stmtLock->execute([
                ':doctor_id' => $doctorId,
                ':date'      => $appDate
            ]);
            $bookedAppts = $stmtLock->fetchAll(PDO::FETCH_ASSOC);

            // Re-verify: Kiểm tra xem bác sĩ có bị đặt lịch trùng trong vòng 30 phút không
            foreach ($bookedAppts as $appt) {
                $apptTime = strtotime($appt['appointment_date']);
                if (abs($appDateTime - $apptTime) < 1800) {
                    $conn->rollBack();
                    return $this->sendError('Khung giờ này của bác sĩ đã bị đặt lịch bởi bệnh nhân khác.', 409);
                }
            }

            // Re-verify: Kiểm tra bệnh nhân không được có 2 lịch hẹn đang hoạt động trùng giờ (cách nhau < 30 phút)
            $sqlPatLock = "SELECT id, appointment_date FROM appointments 
                           WHERE patient_id = :patient_id 
                             AND DATE(appointment_date) = :date 
                             AND status NOT IN ('cancelled') 
                             AND deleted_at IS NULL 
                           FOR UPDATE";
            $stmtPatLock = $conn->prepare($sqlPatLock);
            $stmtPatLock->execute([
                ':patient_id' => $patientId,
                ':date'       => $appDate
            ]);
            $patientBookings = $stmtPatLock->fetchAll(PDO::FETCH_ASSOC);

            foreach ($patientBookings as $appt) {
                $apptTime = strtotime($appt['appointment_date']);
                if (abs($appDateTime - $apptTime) < 1800) {
                    $conn->rollBack();
                    return $this->sendError('Bạn đã có một lịch hẹn hoạt động khác trùng khung giờ này.', 409);
                }
            }

            // Insert lịch hẹn mới vào CSDL với trạng thái 'pending'
            $sqlInsert = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status, created_by) 
                          VALUES (:patient_id, :doctor_id, :appointment_date, :reason, 'pending', :created_by)";
            
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->execute([
                ':patient_id'        => $patientId,
                ':doctor_id'         => $doctorId,
                ':appointment_date'  => $dateTimeStr,
                ':reason'            => $reason,
                ':created_by'        => $user['user_id']
            ]);

            $newApptId = $conn->lastInsertId();

            $conn->commit();

            // Ghi AuditLog đặt lịch
            AuditLog::logCreate('appointments', $newApptId, [
                'patient_id'       => $patientId,
                'doctor_id'        => $doctorId,
                'appointment_date' => $dateTimeStr,
                'reason'           => $reason
            ], $user['user_id']);

            // Tạo notification đặt lịch thành công
            try {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $user['user_id'], 
                    'Đặt lịch khám thành công', 
                    "Lịch hẹn khám bác sĩ {$doctor['name']} vào ngày {$appDate} lúc {$appTime} đã được đặt thành công. Trạng thái: Chờ xác nhận.", 
                    'appointment'
                );
            } catch (Exception $ne) {
                error_log("Failed to create notification on appointment booking: " . $ne->getMessage());
            }

            // Trả về đối tượng vừa tạo
            $newAppt = $this->appointmentModel->findById($newApptId);
            return $this->sendSuccess($newAppt, 'Đặt lịch hẹn khám bệnh thành công', 201);

        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Create appointment transaction error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi hệ thống khi đặt lịch khám.');
        }
    }

    /**
     * GET /api/v1/appointments/my
     */
    public function getMyAppointments() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        $filters = [
            'status'    => $_GET['status'] ?? null,
            'from_date' => $_GET['from_date'] ?? null,
            'to_date'   => $_GET['to_date'] ?? null,
            'page'      => $_GET['page'] ?? 1,
            'limit'     => $_GET['limit'] ?? 10
        ];

        $result = $this->appointmentModel->getPatientAppointmentsForApi($patient['id'], $filters);

        return $this->sendSuccess(
            $result['data'], 
            'Lấy danh sách lịch hẹn thành công', 
            200, 
            $result['meta']
        );
    }

    /**
     * GET /api/v1/appointments/{id}
     */
    public function show($id) {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }

        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            return $this->sendNotFound('Lịch hẹn không tồn tại.');
        }

        // Chống IDOR tuyệt đối: So khớp patient_id
        if ((int)$appointment['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền xem thông tin lịch hẹn này.');
        }

        return $this->sendSuccess($appointment);
    }

    /**
     * POST /api/v1/appointments/{id}/cancel
     */
    public function cancel($id) {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }

        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            return $this->sendNotFound('Lịch hẹn không tồn tại.');
        }

        // Chống IDOR tuyệt đối
        if ((int)$appointment['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền hủy lịch hẹn này.');
        }

        // Chỉ cho phép hủy khi trạng thái là pending hoặc confirmed
        $validStatuses = ['pending', 'confirmed'];
        if (!in_array($appointment['status'], $validStatuses)) {
            return $this->sendError("Trạng thái lịch hẹn là '{$appointment['status']}', không thể hủy.", 400);
        }

        // Chặn hủy lịch trong quá khứ hoặc đã sát giờ khám
        if (strtotime($appointment['appointment_date']) < time()) {
            return $this->sendError('Lịch hẹn đã qua giờ khám hoặc ở quá khứ, không thể hủy.', 400);
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // Cập nhật status thành 'cancelled'
            $sql = "UPDATE appointments SET status = 'cancelled', updated_by = :updated_by WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':updated_by' => $user['user_id'],
                ':id'         => $id
            ]);

            $conn->commit();

            // Ghi AuditLog hủy lịch
            AuditLog::logUpdate('appointments', $id, 
                ['status' => $appointment['status']], 
                ['status' => 'cancelled'],
                $user['user_id']
            );

            // Tạo notification hủy lịch thành công
            try {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $user['user_id'], 
                    'Hủy lịch khám thành công', 
                    "Lịch hẹn khám của bạn vào ngày " . date('Y-m-d H:i', strtotime($appointment['appointment_date'])) . " đã được hủy thành công.", 
                    'appointment'
                );
            } catch (Exception $ne) {
                error_log("Failed to create notification on appointment cancellation: " . $ne->getMessage());
            }

            $updatedAppt = $this->appointmentModel->findById($id);
            return $this->sendSuccess($updatedAppt, 'Hủy lịch hẹn thành công');

        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Cancel appointment error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi khi hủy lịch hẹn.');
        }
    }
}
