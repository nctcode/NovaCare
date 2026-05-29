<?php
/**
 * DepartmentController - Quản lý Khoa
 * Quyền: Admin = full CRUD, Doctor = xem
 */
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../helpers/Security.php';

class DepartmentController {
    private $deptModel;

    public function __construct() {
        $this->deptModel = new Department();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'director']);
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'sort' => $_GET['sort'] ?? 'name_asc',
            'has_doctors' => $_GET['has_doctors'] ?? '',
            'has_nurses' => $_GET['has_nurses'] ?? ''
        ];
        
        $departments = $this->deptModel->getAll($filters);
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        require_once __DIR__ . '/../models/Doctor.php';
        require_once __DIR__ . '/../models/Nurse.php';
        $doctorModel = new Doctor();
        $nurseModel = new Nurse();
        $doctors = $doctorModel->getAll();
        $nurses = $nurseModel->getAll();
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=departments');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
        ];
        $deptId = $this->deptModel->create($data);
        
        $doctorIds = $_POST['doctors'] ?? [];
        $nurseIds = $_POST['nurses'] ?? [];
        $headDoctor = $_POST['head_doctor'] ?? null;
        $headNurse = $_POST['head_nurse'] ?? null;

        if (!empty($headDoctor) && !in_array($headDoctor, $doctorIds)) {
            $doctorIds[] = $headDoctor;
        }
        if (!empty($headNurse) && !in_array($headNurse, $nurseIds)) {
            $nurseIds[] = $headNurse;
        }

        $this->deptModel->assignDoctors($deptId, $doctorIds);
        $this->deptModel->setHeadDoctor($deptId, $headDoctor);

        $this->deptModel->assignNurses($deptId, $nurseIds);
        $this->deptModel->setHeadNurse($deptId, $headNurse);
        
        $_SESSION['success'] = 'Thêm khoa thành công!';
        
        header('Location: index.php?page=departments');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $department = $this->deptModel->findById($id);
        if (!$department) {
            $_SESSION['error'] = 'Không tìm thấy khoa.';
            header('Location: index.php?page=departments');
            exit;
        }
        
        require_once __DIR__ . '/../models/Doctor.php';
        require_once __DIR__ . '/../models/Nurse.php';
        $doctorModel = new Doctor();
        $nurseModel = new Nurse();
        $doctors = $doctorModel->getAll();
        $nurses = $nurseModel->getAll();
        
        // Lấy danh sách ID bác sĩ/y tá thuộc khoa này
        $deptDoctors = array_column($this->deptModel->getDoctorsByDept($id), 'id');
        $deptNurses = array_column($this->deptModel->getNursesByDept($id), 'id');

        // Tìm head_doctor và head_nurse
        $headDoctorId = null;
        foreach ($this->deptModel->getDoctorsByDept($id) as $d) {
            if ($d['is_head'] == 1) $headDoctorId = $d['id'];
        }

        $headNurseId = null;
        foreach ($this->deptModel->getNursesByDept($id) as $n) {
            if ($n['is_head'] == 1) $headNurseId = $n['id'];
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=departments');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
        ];
        $this->deptModel->update($id, $data);
        
        $doctorIds = $_POST['doctors'] ?? [];
        $nurseIds = $_POST['nurses'] ?? [];
        $headDoctor = $_POST['head_doctor'] ?? null;
        $headNurse = $_POST['head_nurse'] ?? null;

        if (!empty($headDoctor) && !in_array($headDoctor, $doctorIds)) {
            $doctorIds[] = $headDoctor;
        }
        if (!empty($headNurse) && !in_array($headNurse, $nurseIds)) {
            $nurseIds[] = $headNurse;
        }

        $this->deptModel->assignDoctors($id, $doctorIds);
        $this->deptModel->setHeadDoctor($id, $headDoctor);

        $this->deptModel->assignNurses($id, $nurseIds);
        $this->deptModel->setHeadNurse($id, $headNurse);
        
        $_SESSION['success'] = 'Cập nhật khoa thành công!';
        
        header('Location: index.php?page=departments');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=departments');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->deptModel->delete($id);
            $_SESSION['success'] = 'Đã xóa khoa.';
        }
        header('Location: index.php?page=departments');
        exit;
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'director']);
        $id = $_GET['id'] ?? null;
        $department = $this->deptModel->findById($id);
        if (!$department) {
            header('Location: index.php?page=departments');
            exit;
        }
        $doctors = $this->deptModel->getDoctorsByDept($id);
        $nurses = $this->deptModel->getNursesByDept($id);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
