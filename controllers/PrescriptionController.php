<?php
/**
 * PrescriptionController - Quản lý đơn thuốc
 * - Doctor: tạo đơn, xem đơn
 * - Patient: xem đơn thuốc của mình
 */
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Medicine.php';

class PrescriptionController {
    private $prescriptionModel;
    private $doctorModel;
    private $patientModel;
    private $medicineModel;

    public function __construct() {
        $this->prescriptionModel = new Prescription();
        $this->doctorModel = new Doctor();
        $this->patientModel = new Patient();
        $this->medicineModel = new Medicine();
    }

    // Danh sách đơn thuốc
    public function index() {
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'admin') {
            $prescriptions = $this->prescriptionModel->getAll();
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $prescriptions = $doctor ? $this->prescriptionModel->getByDoctorId($doctor['id']) : [];
        } elseif ($role === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            $prescriptions = $patient ? $this->prescriptionModel->getByPatientId($patient['id']) : [];
        } else {
            $prescriptions = [];
        }

        $pageTitle = 'Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo đơn thuốc (Doctor)
    public function create() {
        $user = $_SESSION['user'];
        $doctor = $this->doctorModel->findByUserId($user['id']);
        $medicalRecords = $doctor ? $this->prescriptionModel->getMedicalRecordsByDoctorId($doctor['id']) : [];
        $medicines = $this->medicineModel->getAll();

        $pageTitle = 'Tạo Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu đơn thuốc
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_SESSION['user'];
            $doctor = $this->doctorModel->findByUserId($user['id']);

            if (!$doctor) {
                $_SESSION['error'] = 'Không tìm thấy thông tin bác sĩ.';
                header('Location: index.php?page=prescriptions');
                exit;
            }

            // Tạo đơn thuốc
            $prescriptionData = [
                'medical_record_id' => $_POST['medical_record_id'] ?? 0,
                'doctor_id'         => $doctor['id'],
            ];

            try {
                $prescriptionId = $this->prescriptionModel->create($prescriptionData);

                // Thêm các thuốc vào đơn
                $medicineIds   = $_POST['medicine_id'] ?? [];
                $dosages       = $_POST['dosage'] ?? [];
                $durations     = $_POST['duration'] ?? [];
                $instructions  = $_POST['instructions'] ?? [];

                for ($i = 0; $i < count($medicineIds); $i++) {
                    if (!empty($medicineIds[$i])) {
                        $itemData = [
                            'prescription_id' => $prescriptionId,
                            'medicine_id'     => $medicineIds[$i],
                            'dosage'          => $dosages[$i] ?? '',
                            'duration'        => $durations[$i] ?? '',
                            'instructions'    => $instructions[$i] ?? '',
                        ];
                        $this->prescriptionModel->addItem($itemData);
                    }
                }

                $_SESSION['success'] = 'Tạo đơn thuốc thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=prescriptions');
        exit;
    }

    // Xem chi tiết đơn thuốc
    public function view() {
        $id = $_GET['id'] ?? 0;
        $prescription = $this->prescriptionModel->findById($id);
        $items = $this->prescriptionModel->getItems($id);

        if (!$prescription) {
            $_SESSION['error'] = 'Không tìm thấy đơn thuốc.';
            header('Location: index.php?page=prescriptions');
            exit;
        }

        $pageTitle = 'Chi tiết Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
