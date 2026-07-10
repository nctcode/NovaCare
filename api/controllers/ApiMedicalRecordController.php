<?php
/**
 * ApiMedicalRecordController - Quản lý Hồ sơ bệnh án điện tử cho API
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/MedicalRecord.php';
require_once __DIR__ . '/../../models/Prescription.php';

class ApiMedicalRecordController extends BaseApiController {

    private $patientModel;
    private $medicalRecordModel;
    private $prescriptionModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->medicalRecordModel = new MedicalRecord();
        $this->prescriptionModel = new Prescription();
    }

    /**
     * GET /api/v1/records/my
     */
    public function index() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        $filters = [
            'page'  => $_GET['page'] ?? 1,
            'limit' => $_GET['limit'] ?? 10
        ];

        $result = $this->medicalRecordModel->getPatientRecordsForApi($patient['id'], $filters);

        return $this->sendSuccess(
            $result['data'], 
            'Lấy danh sách bệnh án thành công', 
            200, 
            $result['meta']
        );
    }

    /**
     * GET /api/v1/records/{id}
     */
    public function show($id) {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        $record = $this->medicalRecordModel->findById($id);
        if (!$record) {
            return $this->sendNotFound('Bệnh án không tồn tại.');
        }

        // Chống IDOR tuyệt đối
        if ((int)$record['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền truy cập bệnh án này.');
        }

        // Lấy đơn thuốc liên quan nếu có
        $prescriptionData = null;
        $db = new Database();
        $conn = $db->getConnection();
        
        $sqlPres = "SELECT id, status, created_at FROM prescriptions 
                    WHERE medical_record_id = :mr_id LIMIT 1";
        $stmtPres = $conn->prepare($sqlPres);
        $stmtPres->execute([':mr_id' => $id]);
        $pres = $stmtPres->fetch(PDO::FETCH_ASSOC);

        if ($pres) {
            $items = $this->prescriptionModel->getItems($pres['id']);
            $prescriptionData = [
                'id'         => (int)$pres['id'],
                'status'     => $pres['status'],
                'created_at' => $pres['created_at'],
                'medicines'  => array_map(function($item) {
                    return [
                        'medicine_name' => $item['medicine_name'],
                        'quantity'      => (int)$item['quantity'],
                        'dosage'        => $item['dosage'],
                        'duration'      => $item['duration'],
                        'instructions'  => $item['instructions']
                    ];
                }, $items)
            ];
        }

        $data = [
            'id'              => (int)$record['id'],
            'visit_date'      => $record['created_at'],
            'doctor_name'     => $record['doctor_name'],
            'icd10_code'      => $record['icd10_code'],
            'diagnosis'       => $record['diagnosis'],
            'treatment'       => $record['treatment'],
            'notes'           => $record['notes'],
            'reason'          => $record['reason'] ?? null,
            'prescription'    => $prescriptionData
        ];

        return $this->sendSuccess($data, 'Lấy chi tiết bệnh án thành công');
    }
}
