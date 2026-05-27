<?php
/**
 * InvoiceController - Quản lý hóa đơn thanh toán
 * - Cashier: xem tất cả, tạo, thanh toán, hủy
 * - Patient: xem hóa đơn của mình
 */
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class InvoiceController {
    private $invoiceModel;
    private $patientModel;
    private $prescriptionModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->patientModel = new Patient();
        $this->prescriptionModel = new Prescription();
    }

    // Danh sách hóa đơn
    public function index() {
        $user = $_SESSION['user'];

        if ($user['role'] === 'admin' || $user['role'] === 'cashier') {
            $invoices = $this->invoiceModel->getAll();
        } elseif ($user['role'] === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            $invoices = $patient ? $this->invoiceModel->getByPatientId($patient['id']) : [];
        } else {
            $invoices = [];
        }

        // Thống kê
        $stats = [
            'total' => $this->invoiceModel->count(),
            'pending' => $this->invoiceModel->countByStatus('pending'),
            'paid' => $this->invoiceModel->countByStatus('paid'),
            'revenue' => $this->invoiceModel->getTotalRevenue(),
        ];

        $pageTitle = 'Hóa đơn & Thanh toán';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/invoices/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo hóa đơn
    public function create() {
        Security::requireRole(['admin', 'cashier']);
        $patients = $this->invoiceModel->getPatients();
        $services = $this->invoiceModel->getServices();
        $medicines = $this->invoiceModel->getMedicines();

        $pageTitle = 'Tạo hóa đơn mới';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/invoices/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu hóa đơn
    public function store() {
        Security::requireRole(['admin', 'cashier']);
        Security::requirePost('index.php?page=invoices');
        Security::requireCsrf();

        try {
            $user = $_SESSION['user'];
            $discount = floatval($_POST['discount'] ?? 0);

            // Tính tổng
            $items = [];
            $totalAmount = 0;

            if (isset($_POST['item_type']) && is_array($_POST['item_type'])) {
                for ($i = 0; $i < count($_POST['item_type']); $i++) {
                    $qty = intval($_POST['quantity'][$i] ?? 1);
                    $price = floatval($_POST['unit_price'][$i] ?? 0);
                    $amount = $qty * $price;
                    $totalAmount += $amount;
                    
                    $type = $_POST['item_type'][$i];
                    $idVal = $_POST['item_id'][$i] ?: null;
                    
                    $item = [
                        'service_id' => ($type === 'service') ? $idVal : null,
                        'medicine_id' => ($type === 'medicine') ? $idVal : null,
                        'room_id' => ($type === 'room') ? $idVal : null,
                        'description' => $_POST['description'][$i] ?? '',
                        'quantity' => $qty,
                        'unit_price' => $price,
                    ];
                    $items[] = $item;
                }
            }

            $finalAmount = $totalAmount - $discount;

            // Validate discount: không được âm hoặc lớn hơn tổng
            if ($discount < 0) $discount = 0;
            if ($discount > $totalAmount) {
                $_SESSION['error'] = 'Giảm giá không được lớn hơn tổng tiền.';
                header('Location: index.php?page=invoices&action=create');
                exit;
            }
            $finalAmount = $totalAmount - $discount;

            $invoiceData = [
                'patient_id' => $_POST['patient_id'],
                'appointment_id' => $_POST['appointment_id'] ?: null,
                'admission_id' => $_POST['admission_id'] ?: null,
                'prescription_id' => $_POST['prescription_id'] ?: null,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'final_amount' => $finalAmount,
                'payment_method' => $_POST['payment_method'] ?? 'cash',
                'notes' => trim($_POST['notes'] ?? ''),
                'created_by' => $user['id'],
            ];

            $invoiceId = $this->invoiceModel->create($invoiceData);

            // Thêm các items
            foreach ($items as $item) {
                $this->invoiceModel->addItem($invoiceId, $item);
            }

            $_SESSION['success'] = 'Tạo hóa đơn thành công! Mã hóa đơn: #' . $invoiceId;
            AuditLog::logCreate('invoices', $invoiceId, ['patient_id' => $invoiceData['patient_id'], 'final_amount' => $finalAmount]);
            header("Location: index.php?page=invoices&action=detail&id=$invoiceId");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: index.php?page=invoices&action=create');
            exit;
        }
    }

    // Chi tiết hóa đơn
    public function detail() {
        $user = $_SESSION['user'];
        $id = $_GET['id'] ?? 0;
        $invoice = $this->invoiceModel->findById($id);

        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn.';
            header('Location: index.php?page=invoices');
            exit;
        }

        // IDOR check: bệnh nhân chỉ được xem hóa đơn của chính mình
        if ($user['role'] === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            if (!$patient || $invoice['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn này.';
                header('Location: index.php?page=invoices');
                exit;
            }
        } elseif (!Security::hasRole(['admin', 'cashier'])) {
            $_SESSION['error'] = 'Bạn không có quyền xem hóa đơn này.';
            header('Location: index.php?page=invoices');
            exit;
        }

        $items = $this->invoiceModel->getItems($id);

        $pageTitle = 'Chi tiết hóa đơn #' . $id;
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/invoices/detail.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Đánh dấu đã thanh toán
    public function markPaid() {
        Security::requireRole(['admin', 'cashier']);
        Security::requirePost('index.php?page=invoices');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $method = $_POST['method'] ?? 'cash';

        try {
            $this->invoiceModel->markPaid($id, $method);
            AuditLog::logUpdate('invoices', $id, null, ['status' => 'paid', 'method' => $method]);
            $_SESSION['success'] = 'Hóa đơn #' . $id . ' đã được thanh toán!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header("Location: index.php?page=invoices&action=detail&id=$id");
        exit;
    }

    // Hủy hóa đơn
    public function cancel() {
        Security::requireRole(['admin', 'cashier']);
        Security::requirePost('index.php?page=invoices');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $this->invoiceModel->cancel($id);
            AuditLog::logUpdate('invoices', $id, null, ['status' => 'cancelled']);
            $_SESSION['success'] = 'Đã hủy hóa đơn #' . $id;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=invoices');
        exit;
    }

    // Lấy danh sách đơn thuốc chưa thanh toán (JSON)
    public function getUnpaidPrescriptions() {
        Security::requireRole(['admin', 'cashier']);
        $patientId = $_GET['patient_id'] ?? 0;
        $prescriptions = $this->prescriptionModel->getUnpaidByPatientId($patientId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'prescriptions' => $prescriptions
        ]);
        exit;
    }

    // Lấy chi tiết đơn thuốc (JSON)
    public function getPrescriptionDetails() {
        Security::requireRole(['admin', 'cashier']);
        $prescriptionId = $_GET['id'] ?? 0;
        $prescription = $this->prescriptionModel->findById($prescriptionId);
        
        if (!$prescription || $prescription['status'] !== 'draft') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn thuốc chưa thanh toán hợp lệ.']);
            exit;
        }
        
        $items = $this->prescriptionModel->getItems($prescriptionId);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'prescription' => $prescription,
            'items' => $items
        ]);
        exit;
    }
}
