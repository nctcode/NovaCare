<?php
/**
 * ApiDoctorController - Phục vụ dữ liệu Bác sĩ công khai
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Doctor.php';

class ApiDoctorController extends BaseApiController {

    private $doctorModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
    }

    /**
     * GET /api/v1/doctors
     */
    public function index() {
        $filters = [
            'department_id' => $_GET['department_id'] ?? null,
            'keyword'       => $_GET['keyword'] ?? null,
            'page'          => $_GET['page'] ?? 1,
            'limit'         => $_GET['limit'] ?? 10
        ];

        $result = $this->doctorModel->getAllForApi($filters);

        return $this->sendSuccess(
            $result['data'], 
            'Lấy danh sách bác sĩ thành công', 
            200, 
            $result['meta']
        );
    }
}
