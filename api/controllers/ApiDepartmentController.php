<?php
/**
 * ApiDepartmentController - Phục vụ dữ liệu Khoa/Phòng
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Department.php';

class ApiDepartmentController extends BaseApiController {

    private $departmentModel;

    public function __construct() {
        $this->departmentModel = new Department();
    }

    /**
     * GET /api/v1/departments
     */
    public function index() {
        $filters = [];
        $data = $this->departmentModel->getAll($filters);

        $mappedData = array_map(function($dept) {
            return [
                'id' => (int)$dept['id'],
                'name' => $dept['name'],
                'description' => $dept['description']
            ];
        }, $data);

        return $this->sendSuccess($mappedData);
    }
}
