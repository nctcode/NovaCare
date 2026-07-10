<?php
/**
 * ApiPatientController - Controller xử lý thông tin cá nhân và cập nhật hồ sơ bệnh nhân.
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../core/RequestValidator.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../helpers/AuditLog.php';

class ApiPatientController extends BaseApiController {

    private $userModel;
    private $patientModel;

    public function __construct() {
        $this->userModel = new User();
        $this->patientModel = new Patient();
    }

    /**
     * GET /api/v1/patient/profile
     */
    public function getProfile() {
        // Trích xuất thông tin user từ AuthMiddleware
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        // Tải hồ sơ bệnh nhân từ DB dựa trên user_id trong JWT.
        // Chống IDOR tuyệt đối vì không dùng tham số ID truyền từ client.
        $patient = $this->patientModel->findByUserId($user['user_id']);

        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        // Loại bỏ trường nhạy cảm như password/password_hash trước khi trả về
        unset($patient['password']);

        return $this->sendSuccess($patient, 'Lấy thông tin hồ sơ thành công');
    }

    /**
     * PUT /api/v1/patient/profile
     */
    public function updateProfile() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $input = $this->getJsonInput();

        // Lấy thông tin hiện tại từ DB để bảo toàn dữ liệu cũ không cho sửa đổi tự do
        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        $validator = new RequestValidator();
        $validator->required($input, ['name', 'phone'])
                  ->phone($input['phone'] ?? '')
                  ->date($input['date_of_birth'] ?? '')
                  ->inArray($input['gender'] ?? '', ['male', 'female', 'other'], 'gender', 'Giới tính không hợp lệ.')
                  ->inArray($input['blood_type'] ?? '', ['A', 'B', 'AB', 'O', 'A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'], 'blood_type', 'Nhóm máu không hợp lệ.');

        if ($validator->hasErrors()) {
            return $this->sendValidationError($validator->getErrors());
        }

        $name = trim($input['name']);
        $phone = trim($input['phone']);
        $dob = $input['date_of_birth'] ?? null;
        $gender = $input['gender'] ?? 'other';
        $address = isset($input['address']) ? trim($input['address']) : null;
        $bloodType = isset($input['blood_type']) ? trim($input['blood_type']) : null;

        // Kiểm tra số điện thoại mới có trùng lặp với tài khoản khác không
        if ($phone !== $patient['phone']) {
            $existingUser = $this->userModel->findByPhone($phone);
            if ($existingUser && (int)$existingUser['id'] !== (int)$user['user_id']) {
                return $this->sendConflict('Số điện thoại này đã được sử dụng.');
            }
        }

        // Cập nhật thông tin qua model Patient::update (bảo toàn email & tiền sử bệnh)
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            $data = [
                'name'            => $name,
                'email'           => $patient['email'], // Bảo toàn email gốc từ DB, không cho sửa qua API
                'phone'           => $phone,
                'date_of_birth'   => $dob,
                'gender'          => $gender,
                'address'         => $address,
                'blood_type'      => $bloodType,
                'medical_history' => $patient['medical_history'] // Bảo toàn tiền sử bệnh án gốc
            ];

            $result = $this->patientModel->update($patient['id'], $data);

            if (!$result) {
                throw new Exception("Lỗi ghi dữ liệu cập nhật bệnh nhân.");
            }

            // Đồng bộ tên và số điện thoại sang bảng users
            $this->userModel->updateNamePhone($user['user_id'], $name, $phone);

            $conn->commit();

            // Ghi nhận Audit Log chỉnh sửa hồ sơ
            AuditLog::logUpdate(
                'patients', 
                $patient['id'], 
                ['name' => $patient['name'], 'phone' => $patient['phone']], 
                ['name' => $name, 'phone' => $phone],
                $user['user_id']
            );

            // Lấy lại dữ liệu hồ sơ mới nhất để trả về
            $updatedPatient = $this->patientModel->findByUserId($user['user_id']);
            unset($updatedPatient['password']);

            return $this->sendSuccess($updatedPatient, 'Cập nhật hồ sơ bệnh nhân thành công');

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Update profile transaction failed: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi hệ thống khi cập nhật hồ sơ. Vui lòng thử lại.');
        }
    }
}
