<?php
/**
 * ShiftController - Quản lý ca trực
 * - Doctor/Nurse: đăng ký ca trực
 * - Admin: xem tổng quan, tạo ca trực mới
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
        $shifts = $this->shiftModel->getAll();

        // Lấy doctor_id nếu là doctor
        $doctorId = null;
        $myShifts = [];
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $doctorId = $doctor['id'];
                $myShifts = $this->shiftModel->getShiftsByDoctorId($doctorId);
            }
        }

        $pageTitle = 'Quản lý Ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo ca trực (Admin)
    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Tạo Ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu ca trực mới (Admin)
    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=shifts');
        Security::requireCsrf();

        $data = [
            'shift_date' => $_POST['shift_date'] ?? '',
            'shift_type' => $_POST['shift_type'] ?? 'day',
        ];

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

        // Xác định staff ID theo role
        $staffId = null;
        $staffType = $user['role']; // 'doctor' hoặc 'nurse'

        if ($staffType === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor) {
                $_SESSION['error'] = 'Không tìm thấy thông tin bác sĩ.';
                header('Location: index.php?page=shifts');
                exit;
            }
            $staffId = $doctor['id'];
        } else {
            $nurse = $this->nurseModel->findByUserId($user['id']);
            if (!$nurse) {
                $_SESSION['error'] = 'Không tìm thấy thông tin y tá.';
                header('Location: index.php?page=shifts');
                exit;
            }
            $staffId = $nurse['id'];
        }

        // Kiểm tra rule: tối đa 2 ca night/tuần
        $shift = $this->shiftModel->findById($shiftId);
        if ($shift && $shift['shift_type'] === 'night') {
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

        if ($staffType === 'doctor') {
            $result = $this->shiftModel->registerShift($staffId, $shiftId);
        } else {
            $result = $this->shiftModel->registerNurseShift($staffId, $shiftId);
        }

        switch ($result) {
            case 'success':
                $_SESSION['success'] = 'Đăng ký ca trực thành công!';
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

    // Hủy đăng ký ca trực (Doctor/Nurse - POST only + CSRF)
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
}
