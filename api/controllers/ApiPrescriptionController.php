<?php
/**
 * ApiPrescriptionController - Quản lý Đơn thuốc phục vụ API
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Prescription.php';

class ApiPrescriptionController extends BaseApiController {

    private $patientModel;
    private $prescriptionModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->prescriptionModel = new Prescription();
    }

    /**
     * GET /api/v1/prescriptions/my
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
            'status' => $_GET['status'] ?? null,
            'page'   => $_GET['page'] ?? 1,
            'limit'  => $_GET['limit'] ?? 10
        ];

        $result = $this->prescriptionModel->getPatientPrescriptionsForApi($patient['id'], $filters);

        return $this->sendSuccess(
            $result['data'], 
            'Lấy danh sách đơn thuốc thành công', 
            200, 
            $result['meta']
        );
    }

    /**
     * GET /api/v1/prescriptions/{id}
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

        $prescription = $this->prescriptionModel->findById($id);
        if (!$prescription) {
            return $this->sendNotFound('Đơn thuốc không tồn tại.');
        }

        // Chống IDOR tuyệt đối
        if ((int)$prescription['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền xem đơn thuốc này.');
        }

        // Lấy danh sách thuốc trong đơn
        $items = $this->prescriptionModel->getItems($id);

        $medicines = array_map(function($item) {
            return [
                'medicine_name' => $item['medicine_name'],
                'dosage'        => $item['dosage'],
                'duration'      => $item['duration'],
                'instructions'  => $item['instructions'],
                'quantity'      => (int)$item['quantity']
            ];
        }, $items);

        $data = [
            'id'          => (int)$prescription['id'],
            'created_at'  => $prescription['created_at'],
            'doctor_name' => $prescription['doctor_name'],
            'diagnosis'   => $prescription['diagnosis'],
            'status'      => $prescription['status'],
            'medicines'   => $medicines
        ];

        return $this->sendSuccess($data, 'Lấy chi tiết đơn thuốc thành công');
    }
}
