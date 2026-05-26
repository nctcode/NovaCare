<?php
/**
 * ShiftController - Quản lý ca trực
 * - Doctor/Nurse: đăng ký ca trực
 * - Admin: xem tổng quan
 * - Trưởng khoa / Điều dưỡng trưởng: tạo ca trực và duyệt đăng ký
 */
require_once __DIR__ . '/../models/Shift.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Nurse.php';
require_once __DIR__ . '/../helpers/Security.php';

class ShiftController {
    private $shiftModel;
    private $doctorModel;
    private $nurseModel;

    public function __construct() {
        $this->shiftModel = new Shift();
        $this->doctorModel = new Doctor();
        $this->nurseModel = new Nurse();
    }

    // Danh sách ca trực
    public function index() {
        $user = $_SESSION['user'];
        
        // Lấy khoa của người dùng hiện tại
        $departmentId = null;
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $departmentId = $doctor['department_id'];
            }
        } elseif ($user['role'] === 'nurse') {
            $nurse = $this->nurseModel->findByUserId($user['id']);
            if ($nurse) {
                $departmentId = $nurse['department_id'];
            }
        }

        // Lấy ca trực: Admin xem tất cả, nhân viên chỉ xem ca trực thuộc khoa mình
        if ($user['role'] === 'admin') {
            $shifts = $this->shiftModel->getAll(null);
        } else {
            $shifts = $this->shiftModel->getAll($departmentId);
        }

        // Lấy ca trực đã đăng ký của user hiện tại
        $myShifts = [];
        $myShiftStatuses = [];
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $registered = $this->shiftModel->getShiftsByDoctorId($doctor['id']);
                foreach ($registered as $rs) {
                    $myShifts[] = $rs['id'];
                    $myShiftStatuses[$rs['id']] = $rs['registration_status'];
                }
            }
        } elseif ($user['role'] === 'nurse') {
            $nurse = $this->nurseModel->findByUserId($user['id']);
            if ($nurse) {
                $registered = $this->shiftModel->getShiftsByNurseId($nurse['id']);
                foreach ($registered as $rs) {
                    $myShifts[] = $rs['id'];
                    $myShiftStatuses[$rs['id']] = $rs['registration_status'];
                }
            }
        }

        // Kiểm tra xem có phải trưởng khoa / điều dưỡng trưởng không
        $isHead = Security::isHeadOfDepartment() !== false;

        $pageTitle = 'Quản lý Ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo ca trực (Trưởng khoa / Điều dưỡng trưởng)
    public function create() {
        Security::requireHeadRole();
        $pageTitle = 'Tạo Ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu ca trực mới (Trưởng khoa / Điều dưỡng trưởng)
    public function store() {
        $headInfo = Security::requireHeadRole();
        Security::requirePost('index.php?page=shifts');
        Security::requireCsrf();

        // Tự động gán khoa của trưởng khoa tạo ca
        $departmentId = $headInfo['department_id'];

        $data = [
            'department_id'    => $departmentId,
            'name'             => trim($_POST['name'] ?? ''),
            'shift_date'       => $_POST['shift_date'] ?? '',
            'start_time'       => $_POST['start_time'] ?? '',
            'end_time'         => $_POST['end_time'] ?? '',
            'required_doctors' => (int)($_POST['required_doctors'] ?? 0),
            'required_nurses'  => (int)($_POST['required_nurses'] ?? 0),
            'shift_type'       => $_POST['shift_type'] ?? 'day',
            'notes'            => trim($_POST['notes'] ?? ''),
        ];

        // Validation
        if (empty($data['name']) || empty($data['shift_date']) || empty($data['start_time']) || empty($data['end_time'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ các trường thông tin bắt buộc.';
            header('Location: index.php?page=shifts&action=create');
            exit;
        }

        try {
            $this->shiftModel->create($data);
            $_SESSION['success'] = 'Tạo ca trực thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=shifts');
        exit;
    }

    // Đăng ký ca trực (Doctor/Nurse)
    public function register() {
        Security::requireRole(['doctor', 'nurse']);
        $user = $_SESSION['user'];
        $shiftId = $_GET['shift_id'] ?? 0;

        $shift = $this->shiftModel->findById($shiftId);
        if (!$shift) {
            $_SESSION['error'] = 'Không tìm thấy ca trực.';
            header('Location: index.php?page=shifts');
            exit;
        }

        // Xác định staff ID và khoa
        $staffId = null;
        $staffType = $user['role'];
        $userDeptId = null;

        if ($staffType === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor) {
                $_SESSION['error'] = 'Không tìm thấy thông tin bác sĩ.';
                header('Location: index.php?page=shifts');
                exit;
            }
            $staffId = $doctor['id'];
            $userDeptId = $doctor['department_id'];
        } else {
            $nurse = $this->nurseModel->findByUserId($user['id']);
            if (!$nurse) {
                $_SESSION['error'] = 'Không tìm thấy thông tin y tá.';
                header('Location: index.php?page=shifts');
                exit;
            }
            $staffId = $nurse['id'];
            $userDeptId = $nurse['department_id'];
        }

        // Kiểm tra xem ca trực có đúng khoa không
        if ($shift['department_id'] !== null && $shift['department_id'] != $userDeptId) {
            $_SESSION['error'] = 'Bạn chỉ có thể đăng ký ca trực của khoa mình.';
            header('Location: index.php?page=shifts');
            exit;
        }

        // Kiểm tra rule: tối đa 2 ca night/tuần
        if ($shift['shift_type'] === 'night') {
            $shiftDate = $shift['shift_date'];
            $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($shiftDate)));
            if ($staffType === 'doctor') {
                $nightCount = $this->shiftModel->countNightShiftsInWeek($staffId, $weekStart);
            } else {
                $nightCount = $this->shiftModel->countNurseNightShiftsInWeek($staffId, $weekStart);
            }
            if ($nightCount >= 2) {
                $_SESSION['error'] = 'Bạn đã đăng ký đủ 2 ca trực đêm trong tuần này. Không thể đăng ký thêm.';
                header('Location: index.php?page=shifts');
                exit;
            }
        }

        // Thực hiện đăng ký
        if ($staffType === 'doctor') {
            $result = $this->shiftModel->registerShift($staffId, $shiftId);
        } else {
            $result = $this->shiftModel->registerNurseShift($staffId, $shiftId);
        }

        switch ($result) {
            case 'success':
                $_SESSION['success'] = 'Đăng ký ca trực thành công! Vui lòng chờ Trưởng khoa duyệt.';
                break;
            case 'already_registered':
                $_SESSION['error'] = 'Bạn đã đăng ký ca trực này rồi.';
                break;
            case 'night_shift_full':
                $_SESSION['error'] = 'Ca trực đêm đã đủ 20 người. Không thể đăng ký thêm.';
                break;
        }

        header('Location: index.php?page=shifts');
        exit;
    }

    // Hủy đăng ký ca trực (POST + CSRF)
    public function unregister() {
        Security::requireRole(['doctor', 'nurse']);
        Security::requirePost('index.php?page=shifts');
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $shiftId = $_POST['shift_id'] ?? 0;

        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $this->shiftModel->unregisterShift($doctor['id'], $shiftId);
                $_SESSION['success'] = 'Hủy đăng ký ca trực thành công!';
            }
        } else {
            $nurse = $this->nurseModel->findByUserId($user['id']);
            if ($nurse) {
                $this->shiftModel->unregisterNurseShift($nurse['id'], $shiftId);
                $_SESSION['success'] = 'Hủy đăng ký ca trực thành công!';
            }
        }

        header('Location: index.php?page=shifts');
        exit;
    }

    // Quản lý các đăng ký (Dành cho trưởng khoa duyệt)
    public function manage() {
        $headInfo = Security::requireHeadRole();
        $shiftId = $_GET['shift_id'] ?? 0;

        $shift = $this->shiftModel->findById($shiftId);
        if (!$shift) {
            $_SESSION['error'] = 'Không tìm thấy ca trực.';
            header('Location: index.php?page=shifts');
            exit;
        }

        // Kiểm tra ca trực thuộc khoa của trưởng khoa đang quản lý
        if ($shift['department_id'] !== null && $shift['department_id'] != $headInfo['department_id']) {
            $_SESSION['error'] = 'Bạn không có quyền quản lý ca trực của khoa khác.';
            header('Location: index.php?page=shifts');
            exit;
        }

        $registrations = $this->shiftModel->getPendingRegistrations($shiftId);

        $pageTitle = 'Duyệt đăng ký ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/manage.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Duyệt đăng ký (POST + CSRF)
    public function approve() {
        $headInfo = Security::requireHeadRole();
        Security::requirePost('index.php?page=shifts');
        Security::requireCsrf();

        $role = $_POST['role'] ?? '';
        $registrationId = $_POST['registration_id'] ?? 0;
        $shiftId = $_POST['shift_id'] ?? 0;

        if (!in_array($role, ['doctor', 'nurse']) || !$registrationId || !$shiftId) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            header('Location: index.php?page=shifts');
            exit;
        }

        $shift = $this->shiftModel->findById($shiftId);
        if (!$shift || ($shift['department_id'] !== null && $shift['department_id'] != $headInfo['department_id'])) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này.';
            header('Location: index.php?page=shifts');
            exit;
        }

        if ($this->shiftModel->approveRegistration($role, $registrationId)) {
            $_SESSION['success'] = 'Duyệt ca trực thành công!';
        } else {
            $_SESSION['error'] = 'Không thể duyệt đăng ký.';
        }

        header('Location: index.php?page=shifts&action=manage&shift_id=' . $shiftId);
        exit;
    }

    // Từ chối đăng ký (POST + CSRF)
    public function reject() {
        $headInfo = Security::requireHeadRole();
        Security::requirePost('index.php?page=shifts');
        Security::requireCsrf();

        $role = $_POST['role'] ?? '';
        $registrationId = $_POST['registration_id'] ?? 0;
        $shiftId = $_POST['shift_id'] ?? 0;

        if (!in_array($role, ['doctor', 'nurse']) || !$registrationId || !$shiftId) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            header('Location: index.php?page=shifts');
            exit;
        }

        $shift = $this->shiftModel->findById($shiftId);
        if (!$shift || ($shift['department_id'] !== null && $shift['department_id'] != $headInfo['department_id'])) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này.';
            header('Location: index.php?page=shifts');
            exit;
        }

        if ($this->shiftModel->rejectRegistration($role, $registrationId)) {
            $_SESSION['success'] = 'Đã từ chối đăng ký ca trực.';
        } else {
            $_SESSION['error'] = 'Không thể từ chối đăng ký.';
        }

        header('Location: index.php?page=shifts&action=manage&shift_id=' . $shiftId);
        exit;
    }
}
