<?php
/**
 * UserController - Quản lý tài khoản người dùng và phân quyền
 */
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';
require_once __DIR__ . '/../models/Department.php';

class UserController {
    private $userModel;

    public function __construct() {
        if (!isset($_SESSION['user']) || !Security::hasRole('admin')) {
            header('Location: index.php?page=login');
            exit;
        }
        $this->userModel = new User();
    }

    // Hiển thị danh sách người dùng phân loại vai trò
    public function index() {
        $activeRole = $_GET['role'] ?? 'all';
        $users = [];

        // Thống kê số lượng theo vai trò để hiển thị trên badge
        $rolesCount = [
            'all'          => count($this->userModel->getAll()),
            'admin'        => $this->userModel->countByRole('admin'),
            'doctor'       => $this->userModel->countByRole('doctor'),
            'nurse'        => $this->userModel->countByRole('nurse'),
            'technician'   => $this->userModel->countByRole('technician'),
            'receptionist' => $this->userModel->countByRole('receptionist'),
            'pharmacist'   => $this->userModel->countByRole('pharmacist'),
            'cashier'      => $this->userModel->countByRole('cashier'),
            'director'     => $this->userModel->countByRole('director'),
            'patient'      => $this->userModel->countByRole('patient'),
        ];

        // Lấy dữ liệu phù hợp với vai trò
        if ($activeRole === 'all') {
            $users = $this->userModel->getAll();
        } elseif ($activeRole === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $users = $doctorModel->getAll();
        } elseif ($activeRole === 'nurse') {
            require_once __DIR__ . '/../models/Nurse.php';
            $nurseModel = new Nurse();
            $users = $nurseModel->getAll();
        } elseif ($activeRole === 'technician') {
            require_once __DIR__ . '/../models/Technician.php';
            $techModel = new Technician();
            $users = $techModel->getAll();
        } elseif ($activeRole === 'patient') {
            require_once __DIR__ . '/../models/Patient.php';
            $patientModel = new Patient();
            $users = $patientModel->getAll();
        } else {
            // Với admin, receptionist, pharmacist, cashier, director
            $allUsers = $this->userModel->getAll();
            foreach ($allUsers as $u) {
                if ($u['role'] === $activeRole) {
                    $users[] = $u;
                }
            }
        }

        $pageTitle = 'Quản lý Người dùng';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/users/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Hiển thị form thêm mới
    public function create() {
        $deptModel = new Department();
        $departments = $deptModel->getAll();
        
        $pageTitle = 'Thêm Người dùng';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/users/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xử lý thêm mới
    public function store() {
        Security::requirePost('index.php?page=users');
        Security::requireCsrf();

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '123456',
            'phone'    => trim($_POST['phone'] ?? ''),
            'role'     => $_POST['role'] ?? 'patient'
        ];

        // Validate cơ bản
        if (empty($data['name']) || empty($data['email']) || empty($data['role'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
            header('Location: index.php?page=users&action=create');
            exit;
        }

        // Validate role
        $validRoles = ['admin','doctor','nurse','patient','receptionist','pharmacist','technician','director','cashier'];
        if (!in_array($data['role'], $validRoles)) {
            $_SESSION['error'] = 'Vai trò không hợp lệ!';
            header('Location: index.php?page=users&action=create');
            exit;
        }

        // Kiểm tra email tồn tại
        if ($this->userModel->findByEmail($data['email'])) {
            $_SESSION['error'] = 'Email này đã được sử dụng!';
            header('Location: index.php?page=users&action=create');
            exit;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // 1. Tạo user
            $userId = $this->userModel->create($data);

            // 2. Tạo bản ghi đặc thù cho vai trò
            if ($data['role'] === 'doctor') {
                $department_ids = $_POST['department_ids'] ?? [];
                $specialty = trim($_POST['specialty'] ?? '');
                $experience_years = intval($_POST['experience_years'] ?? 0);
                $firstDeptId = !empty($department_ids) ? $department_ids[0] : null;

                $stmt = $conn->prepare("INSERT INTO doctors (user_id, department_id, specialty, experience_years) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $firstDeptId, $specialty, $experience_years]);
                $doctorId = $conn->lastInsertId();

                if (!empty($department_ids)) {
                    $stmtDD = $conn->prepare("INSERT INTO doctor_departments (doctor_id, department_id) VALUES (?, ?)");
                    foreach ($department_ids as $deptId) {
                        $stmtDD->execute([$doctorId, $deptId]);
                    }
                }
            } elseif ($data['role'] === 'nurse') {
                $department_id = $_POST['department_id'] ?: null;
                $stmt = $conn->prepare("INSERT INTO nurses (user_id, department_id) VALUES (?, ?)");
                $stmt->execute([$userId, $department_id]);
            } elseif ($data['role'] === 'technician') {
                $department_id = $_POST['department_id'] ?: null;
                $specialty = trim($_POST['specialty'] ?? '');
                $stmt = $conn->prepare("INSERT INTO technicians (user_id, department_id, specialty) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $department_id, $specialty]);
            } elseif ($data['role'] === 'patient') {
                $date_of_birth = $_POST['date_of_birth'] ?: null;
                $gender = $_POST['gender'] ?? 'Nam';
                $address = trim($_POST['address'] ?? '');
                $blood_type = $_POST['blood_type'] ?? '';
                $medical_history = trim($_POST['medical_history'] ?? '');

                $stmt = $conn->prepare("INSERT INTO patients (user_id, date_of_birth, gender, address, blood_type, medical_history) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $date_of_birth, $gender, $address, $blood_type, $medical_history]);
            }

            $conn->commit();

            AuditLog::log('user_created', 'users', $userId, null, [
                'name' => $data['name'],
                'role' => $data['role']
            ]);

            $_SESSION['success'] = 'Thêm tài khoản thành công!';
            header('Location: index.php?page=users&role=' . $data['role']);
            exit;
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['error'] = 'Lỗi khi tạo tài khoản: ' . $e->getMessage();
            header('Location: index.php?page=users&action=create');
            exit;
        }
    }

    // Hiển thị form cập nhật
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=users');
            exit;
        }

        $editUser = $this->userModel->findById($id);
        if (!$editUser) {
            $_SESSION['error'] = 'Không tìm thấy người dùng!';
            header('Location: index.php?page=users');
            exit;
        }

        // Load dữ liệu đặc thù
        $roleData = null;
        if ($editUser['role'] === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $roleData = $doctorModel->findByUserId($id);
        } elseif ($editUser['role'] === 'nurse') {
            require_once __DIR__ . '/../models/Nurse.php';
            $nurseModel = new Nurse();
            $roleData = $nurseModel->findByUserId($id);
        } elseif ($editUser['role'] === 'technician') {
            require_once __DIR__ . '/../models/Technician.php';
            $techModel = new Technician();
            $roleData = $techModel->findByUserId($id);
        } elseif ($editUser['role'] === 'patient') {
            require_once __DIR__ . '/../models/Patient.php';
            $patientModel = new Patient();
            $roleData = $patientModel->findByUserId($id);
        }

        $deptModel = new Department();
        $departments = $deptModel->getAll();

        $pageTitle = 'Sửa Người dùng';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/users/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xử lý cập nhật
    public function update() {
        Security::requirePost('index.php?page=users');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=users');
            exit;
        }

        $editUser = $this->userModel->findById($id);
        if (!$editUser) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản!';
            header('Location: index.php?page=users');
            exit;
        }

        $data = [
            'name'  => trim($_POST['name'] ?? $editUser['name']),
            'email' => trim($_POST['email'] ?? $editUser['email']),
            'phone' => trim($_POST['phone'] ?? $editUser['phone']),
            'role'  => $_POST['role'] ?? $editUser['role']
        ];

        // Nếu đổi email, kiểm tra xem email mới đã tồn tại chưa
        if ($data['email'] !== $editUser['email']) {
            if ($this->userModel->findByEmail($data['email'])) {
                $_SESSION['error'] = 'Email này đã được sử dụng!';
                header('Location: index.php?page=users&action=edit&id=' . $id);
                exit;
            }
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // 1. Cập nhật bảng users
            $this->userModel->update($id, $data);
            
            // Cập nhật mật khẩu nếu có
            if (!empty($_POST['password'])) {
                $hashedPassword = Security::hashPassword($_POST['password']);
                $this->userModel->updatePassword($id, $hashedPassword);
            }

            // 2. Đồng bộ vai trò và các bảng đặc thù
            $oldRole = $editUser['role'];
            $newRole = $data['role'];

            // Nếu vai trò thay đổi, dọn dẹp bảng cũ
            if ($oldRole !== $newRole) {
                if ($oldRole === 'doctor') {
                    $conn->prepare("UPDATE doctors SET deleted_at = NOW() WHERE user_id = ?")->execute([$id]);
                } elseif ($oldRole === 'nurse') {
                    $conn->prepare("DELETE FROM nurses WHERE user_id = ?")->execute([$id]);
                } elseif ($oldRole === 'technician') {
                    $conn->prepare("DELETE FROM technicians WHERE user_id = ?")->execute([$id]);
                } elseif ($oldRole === 'patient') {
                    $conn->prepare("UPDATE patients SET deleted_at = NOW() WHERE user_id = ?")->execute([$id]);
                }
            }

            // Lưu dữ liệu vai trò mới/hiện tại
            if ($newRole === 'doctor') {
                require_once __DIR__ . '/../models/Doctor.php';
                $doctorModel = new Doctor();
                $doc = $doctorModel->findByUserId($id);
                $docData = [
                    'name'             => $data['name'],
                    'email'            => $data['email'],
                    'phone'            => $data['phone'],
                    'department_ids'   => $_POST['department_ids'] ?? [],
                    'specialty'        => trim($_POST['specialty'] ?? ''),
                    'experience_years' => intval($_POST['experience_years'] ?? 0),
                ];

                if ($doc) {
                    $doctorModel->update($doc['id'], $docData);
                } else {
                    $firstDeptId = !empty($docData['department_ids']) ? $docData['department_ids'][0] : null;
                    $stmt = $conn->prepare("INSERT INTO doctors (user_id, department_id, specialty, experience_years) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$id, $firstDeptId, $docData['specialty'], $docData['experience_years']]);
                    $doctorId = $conn->lastInsertId();
                    
                    if (!empty($docData['department_ids'])) {
                        $stmtDD = $conn->prepare("INSERT INTO doctor_departments (doctor_id, department_id) VALUES (?, ?)");
                        foreach ($docData['department_ids'] as $deptId) {
                            $stmtDD->execute([$doctorId, $deptId]);
                        }
                    }
                }
            } elseif ($newRole === 'nurse') {
                $stmtCheck = $conn->prepare("SELECT id FROM nurses WHERE user_id = ?");
                $stmtCheck->execute([$id]);
                $nurseId = $stmtCheck->fetchColumn();
                $deptId = $_POST['department_id'] ?: null;
                if ($nurseId) {
                    $conn->prepare("UPDATE nurses SET department_id = ? WHERE id = ?")->execute([$deptId, $nurseId]);
                } else {
                    $conn->prepare("INSERT INTO nurses (user_id, department_id) VALUES (?, ?)")->execute([$id, $deptId]);
                }
            } elseif ($newRole === 'technician') {
                $stmtCheck = $conn->prepare("SELECT id FROM technicians WHERE user_id = ?");
                $stmtCheck->execute([$id]);
                $techId = $stmtCheck->fetchColumn();
                $deptId = $_POST['department_id'] ?: null;
                $specialty = trim($_POST['specialty'] ?? '');
                if ($techId) {
                    $conn->prepare("UPDATE technicians SET department_id = ?, specialty = ? WHERE id = ?")->execute([$deptId, $specialty, $techId]);
                } else {
                    $conn->prepare("INSERT INTO technicians (user_id, department_id, specialty) VALUES (?, ?, ?)")->execute([$id, $deptId, $specialty]);
                }
            } elseif ($newRole === 'patient') {
                require_once __DIR__ . '/../models/Patient.php';
                $patientModel = new Patient();
                $pat = $patientModel->findByUserId($id);
                $patData = [
                    'name'            => $data['name'],
                    'email'           => $data['email'],
                    'phone'           => $data['phone'],
                    'date_of_birth'   => $_POST['date_of_birth'] ?: null,
                    'gender'          => $_POST['gender'] ?? 'Nam',
                    'address'         => trim($_POST['address'] ?? ''),
                    'blood_type'      => $_POST['blood_type'] ?? '',
                    'medical_history' => trim($_POST['medical_history'] ?? '')
                ];

                if ($pat) {
                    $patientModel->update($pat['id'], $patData);
                } else {
                    $stmt = $conn->prepare("INSERT INTO patients (user_id, date_of_birth, gender, address, blood_type, medical_history) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$id, $patData['date_of_birth'], $patData['gender'], $patData['address'], $patData['blood_type'], $patData['medical_history']]);
                }
            }

            $conn->commit();

            AuditLog::log('user_updated', 'users', $id, null, $data);

            $_SESSION['success'] = 'Cập nhật tài khoản thành công!';
            header('Location: index.php?page=users&role=' . $newRole);
            exit;
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['error'] = 'Lỗi khi cập nhật tài khoản: ' . $e->getMessage();
            header('Location: index.php?page=users&action=edit&id=' . $id);
            exit;
        }
    }

    // Xem chi tiết tài khoản
    public function view() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=users');
            exit;
        }

        $viewUser = $this->userModel->findById($id);
        if (!$viewUser) {
            $_SESSION['error'] = 'Không tìm thấy người dùng!';
            header('Location: index.php?page=users');
            exit;
        }

        // Load dữ liệu đặc thù của vai trò
        $roleData = null;
        if ($viewUser['role'] === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $roleData = $doctorModel->findByUserId($id);
        } elseif ($viewUser['role'] === 'nurse') {
            require_once __DIR__ . '/../models/Nurse.php';
            $nurseModel = new Nurse();
            $roleData = $nurseModel->findByUserId($id);
        } elseif ($viewUser['role'] === 'technician') {
            require_once __DIR__ . '/../models/Technician.php';
            $techModel = new Technician();
            $roleData = $techModel->findByUserId($id);
        } elseif ($viewUser['role'] === 'patient') {
            require_once __DIR__ . '/../models/Patient.php';
            $patientModel = new Patient();
            $roleData = $patientModel->findByUserId($id);
        }

        $pageTitle = 'Chi tiết Người dùng';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/users/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xử lý xóa
    public function delete() {
        Security::requirePost('index.php?page=users');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        
        // Không cho phép xóa chính mình
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Bạn không thể xóa tài khoản của chính mình!';
            header('Location: index.php?page=users');
            exit;
        }

        $u = $this->userModel->findById($id);
        if (!$u) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản cần xóa!';
            header('Location: index.php?page=users');
            exit;
        }

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // 1. Soft delete ở bảng users
            $this->userModel->delete($id);

            // 2. Dọn dẹp/Soft delete ở bảng đặc thù
            if ($u['role'] === 'doctor') {
                $conn->prepare("UPDATE doctors SET deleted_at = NOW() WHERE user_id = ?")->execute([$id]);
            } elseif ($u['role'] === 'nurse') {
                $conn->prepare("DELETE FROM nurses WHERE user_id = ?")->execute([$id]);
            } elseif ($u['role'] === 'technician') {
                $conn->prepare("DELETE FROM technicians WHERE user_id = ?")->execute([$id]);
            } elseif ($u['role'] === 'patient') {
                $conn->prepare("UPDATE patients SET deleted_at = NOW() WHERE user_id = ?")->execute([$id]);
            }

            $conn->commit();

            AuditLog::log('user_deleted', 'users', $id);
            $_SESSION['success'] = 'Xóa tài khoản thành công!';
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            $_SESSION['error'] = 'Lỗi khi xóa tài khoản!';
        }
        header('Location: index.php?page=users');
        exit;
    }
}

