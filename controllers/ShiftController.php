<?php
/**
 * ShiftController - Quản lý ca trực
 * - Doctor/Nurse: đăng ký ca trực
 * - Admin: xem tổng quan, tạo ca trực mới
 */
require_once __DIR__ . '/../models/Shift.php';
require_once __DIR__ . '/../models/Doctor.php';

class ShiftController {
    private $shiftModel;
    private $doctorModel;

    public function __construct() {
        $this->shiftModel = new Shift();
        $this->doctorModel = new Doctor();
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
        $pageTitle = 'Tạo Ca trực';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/shifts/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu ca trực mới (Admin)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        }
        header('Location: index.php?page=shifts');
        exit;
    }

    // Đăng ký ca trực (Doctor/Nurse)
    public function register() {
        $user = $_SESSION['user'];
        $shiftId = $_GET['shift_id'] ?? 0;

        $doctor = $this->doctorModel->findByUserId($user['id']);
        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy thông tin.';
            header('Location: index.php?page=shifts');
            exit;
        }

        $result = $this->shiftModel->registerShift($doctor['id'], $shiftId);

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

    // Hủy đăng ký ca trực
    public function unregister() {
        $user = $_SESSION['user'];
        $shiftId = $_GET['shift_id'] ?? 0;

        $doctor = $this->doctorModel->findByUserId($user['id']);
        if ($doctor) {
            $this->shiftModel->unregisterShift($doctor['id'], $shiftId);
            $_SESSION['success'] = 'Hủy đăng ký ca trực thành công!';
        }

        header('Location: index.php?page=shifts');
        exit;
    }
}
