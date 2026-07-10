<?php
/**
 * ApiInvoiceController - Quản lý Hóa đơn phục vụ API
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Invoice.php';

class ApiInvoiceController extends BaseApiController {

    private $patientModel;
    private $invoiceModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->invoiceModel = new Invoice();
    }

    /**
     * GET /api/v1/invoices/my
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

        $result = $this->invoiceModel->getPatientInvoicesForApi($patient['id'], $filters);

        return $this->sendSuccess(
            $result['data'], 
            'Lấy danh sách hóa đơn thành công', 
            200, 
            $result['meta']
        );
    }

    /**
     * GET /api/v1/invoices/{id}
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

        $invoice = $this->invoiceModel->findById($id);
        if (!$invoice) {
            return $this->sendNotFound('Hóa đơn không tồn tại.');
        }

        // Chống IDOR tuyệt đối
        if ((int)$invoice['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền xem hóa đơn này.');
        }

        // Lấy danh sách các khoản phí chi tiết của hóa đơn
        $items = $this->invoiceModel->getItems($id);

        $tienKham = 0.00;
        $tienDichVu = 0.00;
        $tienThuoc = 0.00;
        $tienGiuong = 0.00;

        $detailedItems = [];

        foreach ($items as $item) {
            $amount = (float)$item['amount'];
            
            // Phân loại chi phí theo service_id, medicine_id, room_id
            if (!empty($item['medicine_id'])) {
                $tienThuoc += $amount;
            } elseif (!empty($item['room_id'])) {
                $tienGiuong += $amount;
            } elseif (!empty($item['service_id'])) {
                // Nếu mô tả có từ khóa khám bệnh thì tính vào tiền khám, ngược lại tính vào tiền dịch vụ/xét nghiệm
                if (mb_stripos($item['description'], 'khám', 0, 'UTF-8') !== false) {
                    $tienKham += $amount;
                } else {
                    $tienDichVu += $amount;
                }
            } else {
                // Dự phòng fallback nếu không gán cụ thể
                if (mb_stripos($item['description'], 'khám', 0, 'UTF-8') !== false) {
                    $tienKham += $amount;
                } else {
                    $tienDichVu += $amount;
                }
            }

            $detailedItems[] = [
                'description' => $item['description'],
                'quantity'    => (int)$item['quantity'],
                'unit_price'  => (float)$item['unit_price'],
                'amount'      => $amount
            ];
        }

        $data = [
            'id'                 => (int)$invoice['id'],
            'invoice_code'       => "INV-" . str_pad($invoice['id'], 6, "0", STR_PAD_LEFT),
            'created_at'         => $invoice['created_at'],
            'payment_status'     => $invoice['status'],
            'total_amount'       => (float)$invoice['total_amount'],
            'discount'           => (float)$invoice['discount'],
            'insurance_discount' => (float)$invoice['insurance_coverage'],
            'final_amount'       => (float)$invoice['final_amount'],
            'patient_payment'    => (float)$invoice['patient_payment'],
            'payment_method'     => $invoice['payment_method'],
            'breakdown'          => [
                'tien_kham'         => $tienKham,
                'tien_dich_vu'      => $tienDichVu,
                'tien_thuoc'        => $tienThuoc,
                'tien_giuong'       => $tienGiuong
            ],
            'items'              => $detailedItems
        ];

        return $this->sendSuccess($data, 'Lấy chi tiết hóa đơn thành công');
    }
}
