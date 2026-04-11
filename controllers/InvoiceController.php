<?php
/**
 * InvoiceController - Quản lý hóa đơn thanh toán
 * - Admin: xem tất cả, tạo, thanh toán, hủy
 * - Patient: xem hóa đơn của mình
 */
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Patient.php';

class InvoiceController {
    private $invoiceModel;
    private $patientModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->patientModel = new Patient();
    }

    // Danh sách hóa đơn
    public function index() {
        $user = $_SESSION['user'];

        if ($user['role'] === 'admin') {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=invoices');
            exit;
        }

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
                    $items[] = [
                        'item_type' => $_POST['item_type'][$i],
                        'item_id' => $_POST['item_id'][$i] ?: null,
                        'description' => $_POST['description'][$i] ?? '',
                        'quantity' => $qty,
                        'unit_price' => $price,
                    ];
                }
            }

            $finalAmount = $totalAmount - $discount;

            $invoiceData = [
                'patient_id' => $_POST['patient_id'],
                'appointment_id' => $_POST['appointment_id'] ?: null,
                'admission_id' => $_POST['admission_id'] ?: null,
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
        $id = $_GET['id'] ?? 0;
        $invoice = $this->invoiceModel->findById($id);

        if (!$invoice) {
            $_SESSION['error'] = 'Không tìm thấy hóa đơn.';
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
        $id = $_GET['id'] ?? 0;
        $method = $_GET['method'] ?? 'cash';

        try {
            $this->invoiceModel->markPaid($id, $method);
            $_SESSION['success'] = 'Hóa đơn #' . $id . ' đã được thanh toán!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header("Location: index.php?page=invoices&action=detail&id=$id");
        exit;
    }

    // Hủy hóa đơn
    public function cancel() {
        $id = $_GET['id'] ?? 0;
        try {
            $this->invoiceModel->cancel($id);
            $_SESSION['success'] = 'Đã hủy hóa đơn #' . $id;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=invoices');
        exit;
    }
}
