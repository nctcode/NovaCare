<?php
/**
 * ApiPaymentController - Quản lý thanh toán hóa đơn cho API
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../core/RequestValidator.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Invoice.php';
require_once __DIR__ . '/../../models/Notification.php';
require_once __DIR__ . '/../../helpers/VNPayHelper.php';
require_once __DIR__ . '/../../helpers/AuditLog.php';

class ApiPaymentController extends BaseApiController {

    private $patientModel;
    private $invoiceModel;
    private $notificationModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->invoiceModel = new Invoice();
        $this->notificationModel = new Notification();
    }

    /**
     * POST /api/v1/payments/vnpay/create
     */
    public function createVNPay() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $input = $this->getJsonInput();
        $invoiceId = isset($input['invoice_id']) ? (int)$input['invoice_id'] : 0;

        if (!$invoiceId) {
            return $this->sendError('Thiếu tham số invoice_id.', 400);
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }

        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            return $this->sendNotFound('Hóa đơn không tồn tại.');
        }

        // Chống IDOR tuyệt đối
        if ((int)$invoice['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền thanh toán hóa đơn này.');
        }

        // Chỉ cho thanh toán hóa đơn có trạng thái pending
        if ($invoice['status'] === 'paid') {
            return $this->sendError('Hóa đơn này đã được thanh toán.', 400);
        }
        if ($invoice['status'] === 'cancelled') {
            return $this->sendError('Hóa đơn này đã bị hủy, không thể thanh toán.', 400);
        }

        $amount = (float)$invoice['final_amount'];
        $returnUrl = $input['return_url'] ?? 'http://localhost/NovaCare/api/v1/payments/vnpay/return';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Tạo transaction_ref duy nhất
        $txnRef = $invoiceId . '_' . time() . '_' . rand(1000, 9999);

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Ghi nhận nỗ lực giao dịch vào bảng payment_transactions
            $sql = "INSERT INTO payment_transactions (invoice_id, patient_id, provider, transaction_ref, amount, status, request_payload) 
                    VALUES (:invoice_id, :patient_id, 'vnpay', :transaction_ref, :amount, 'pending', :payload)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':invoice_id'      => $invoiceId,
                ':patient_id'      => $patient['id'],
                ':transaction_ref' => $txnRef,
                ':amount'          => $amount,
                ':payload'         => json_encode([
                    'return_url' => $returnUrl,
                    'ip_address' => $ipAddress
                ])
            ]);

            $txnId = $conn->lastInsertId();

            // Tạo link thanh toán VNPay
            $paymentData = VNPayHelper::createPaymentUrl($invoiceId, $amount, $returnUrl, $ipAddress);
            
            // Cập nhật lại txnRef đúng chuẩn từ Helper
            $realTxnRef = $paymentData['txn_ref'];
            $sqlUpdateRef = "UPDATE payment_transactions SET transaction_ref = :real_ref WHERE id = :id";
            $conn->prepare($sqlUpdateRef)->execute([
                ':real_ref' => $realTxnRef,
                ':id'       => $txnId
            ]);

            // Ghi AuditLog
            AuditLog::logCreate('payment_transactions', $txnId, [
                'invoice_id'      => $invoiceId,
                'provider'        => 'vnpay',
                'transaction_ref' => $realTxnRef,
                'amount'          => $amount
            ], $user['user_id']);

            return $this->sendSuccess([
                'payment_url'     => $paymentData['url'],
                'invoice_id'      => $invoiceId,
                'amount'          => $amount,
                'provider'        => 'vnpay',
                'transaction_ref' => $realTxnRef
            ], 'Tạo liên kết thanh toán VNPay thành công');

        } catch (Exception $e) {
            error_log("VNPay payment creation error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi khi khởi tạo giao dịch thanh toán.');
        }
    }

    /**
     * POST /api/v1/payments/momo/create
     */
    public function createMoMo() {
        // MoMo chưa được cấu hình trong Phase 2B
        return $this->sendError('MoMo chưa được cấu hình trong Phase 2B.', 501);
    }

    /**
     * GET /api/v1/payments/{invoice_id}/status
     */
    public function status($invoiceId) {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }

        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            return $this->sendNotFound('Hóa đơn không tồn tại.');
        }

        // Chống IDOR
        if ((int)$invoice['patient_id'] !== (int)$patient['id']) {
            return $this->sendForbidden('Bạn không có quyền truy cập thông tin hóa đơn này.');
        }

        $db = new Database();
        $conn = $db->getConnection();

        // Tìm giao dịch thành công mới nhất của hóa đơn này
        $sqlTxn = "SELECT transaction_ref, paid_at FROM payment_transactions 
                   WHERE invoice_id = :invoice_id AND status = 'success' 
                   ORDER BY id DESC LIMIT 1";
        $stmtTxn = $conn->prepare($sqlTxn);
        $stmtTxn->execute([':invoice_id' => $invoiceId]);
        $txn = $stmtTxn->fetch();

        $data = [
            'invoice_id'      => (int)$invoiceId,
            'payment_status'  => $invoice['status'],
            'payment_method'  => $invoice['payment_method'],
            'total_amount'    => (float)$invoice['total_amount'],
            'final_amount'    => (float)$invoice['final_amount'],
            'paid_at'         => $txn['paid_at'] ?? ($invoice['status'] === 'paid' ? $invoice['updated_at'] : null),
            'transaction_ref' => $txn['transaction_ref'] ?? null
        ];

        return $this->sendSuccess($data, 'Lấy trạng thái thanh toán thành công');
    }

    /**
     * GET /api/v1/payments/history
     */
    public function history() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }

        $filters = [
            'status'    => $_GET['status'] ?? null,
            'provider'  => $_GET['provider'] ?? null,
            'from_date' => $_GET['from_date'] ?? null,
            'to_date'   => $_GET['to_date'] ?? null,
            'page'      => $_GET['page'] ?? 1,
            'limit'     => $_GET['limit'] ?? 10
        ];

        $result = $this->invoiceModel->getPaymentHistoryForApi($patient['id'], $filters);

        return $this->sendSuccess(
            $result['data'],
            'Lấy lịch sử thanh toán thành công',
            200,
            $result['meta']
        );
    }

    /**
     * GET /api/v1/payments/vnpay/return
     */
    public function vnpayReturn() {
        $getParams = $_GET;

        // 1. Xác minh chữ ký bảo mật
        if (!VNPayHelper::verifyResponse($getParams)) {
            return $this->sendError('Chữ ký phản hồi VNPay không hợp lệ.', 400);
        }

        $txnRef = $getParams['vnp_TxnRef'] ?? '';
        $amountReceived = isset($getParams['vnp_Amount']) ? (float)$getParams['vnp_Amount'] / 100 : 0.00;
        $responseCode = $getParams['vnp_ResponseCode'] ?? '';

        $db = new Database();
        $conn = $db->getConnection();

        // 2. Kiểm tra giao dịch tồn tại trong hệ thống
        $sqlTxn = "SELECT * FROM payment_transactions WHERE transaction_ref = :txn_ref LIMIT 1";
        $stmtTxn = $conn->prepare($sqlTxn);
        $stmtTxn->execute([':txn_ref' => $txnRef]);
        $txn = $stmtTxn->fetch();

        if (!$txn) {
            return $this->sendNotFound('Không tìm thấy mã giao dịch đối chiếu trong hệ thống.');
        }

        $invoiceId = $txn['invoice_id'];
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            return $this->sendNotFound('Hóa đơn liên kết giao dịch không tồn tại.');
        }

        // 3. Kiểm tra số tiền khớp
        if (abs($amountReceived - (float)$invoice['final_amount']) > 0.01) {
            return $this->sendError('Số tiền thanh toán không khớp với hóa đơn.', 400);
        }

        // 4. Xử lý Idempotent
        if ($txn['status'] === 'success' && $invoice['status'] === 'paid') {
            return $this->sendSuccess([
                'invoice_id' => (int)$invoiceId,
                'status'     => 'paid'
            ], 'Giao dịch đã được cập nhật thành công trước đó (Idempotent).');
        }

        try {
            $conn->beginTransaction();

            // Khóa InnoDB bản ghi giao dịch và hóa đơn để tránh Race Condition
            $conn->prepare("SELECT id FROM payment_transactions WHERE id = :id FOR UPDATE")
                 ->execute([':id' => $txn['id']]);
            $conn->prepare("SELECT id FROM invoices WHERE id = :id FOR UPDATE")
                 ->execute([':id' => $invoiceId]);

            if ($responseCode === '00') {
                // Thanh toán thành công
                $conn->prepare("UPDATE payment_transactions 
                                SET status = 'success', paid_at = NOW(), response_payload = :payload 
                                WHERE id = :id")
                     ->execute([
                         ':payload' => json_encode($getParams),
                         ':id'      => $txn['id']
                     ]);

                $conn->prepare("UPDATE invoices 
                                SET status = 'paid', payment_method = 'vnpay', 
                                    vnpay_txn_ref = :vnp_ref, 
                                    vnpay_transaction_no = :vnp_no, 
                                    vnpay_response_code = :vnp_code 
                                WHERE id = :id")
                     ->execute([
                         ':vnp_ref'  => $txnRef,
                         ':vnp_no'   => $getParams['vnp_TransactionNo'] ?? null,
                         ':vnp_code' => $responseCode,
                         ':id'       => $invoiceId
                     ]);

                // Lấy thông tin user_id của bệnh nhân
                $patientUser = $conn->query("SELECT user_id FROM patients WHERE id = {$txn['patient_id']}")->fetch();
                $userId = $patientUser['user_id'] ?? null;

                // Tạo notification gửi về cho bệnh nhân
                if ($userId) {
                    $this->notificationModel->create(
                        $userId,
                        'Thanh toán thành công',
                        "Hóa đơn mã INV-" . str_pad($invoiceId, 6, '0', STR_PAD_LEFT) . " trị giá " . number_format($invoice['final_amount']) . " VND đã được thanh toán thành công qua cổng VNPay.",
                        'payment'
                    );
                }

                $conn->commit();

                // Ghi AuditLog
                AuditLog::logUpdate('invoices', $invoiceId, ['status' => 'pending'], ['status' => 'paid'], $userId);

                return $this->sendSuccess([
                    'invoice_id' => (int)$invoiceId,
                    'status'     => 'paid'
                ], 'Thanh toán hóa đơn thành công.');

            } else {
                // Thanh toán thất bại
                $conn->prepare("UPDATE payment_transactions 
                                SET status = 'failed', response_payload = :payload 
                                WHERE id = :id")
                     ->execute([
                         ':payload' => json_encode($getParams),
                         ':id'      => $txn['id']
                     ]);

                $conn->commit();
                return $this->sendError('Giao dịch thanh toán thất bại tại cổng VNPay.', 400);
            }

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("VNPay return handler transaction error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi hệ thống khi xử lý kết quả VNPay.');
        }
    }

    /**
     * POST /api/v1/payments/vnpay/ipn
     */
    public function vnpayIpn() {
        // Nhận dữ liệu qua $_GET hoặc $_POST
        $params = !empty($_POST) ? $_POST : $_GET;

        // 1. Xác minh chữ ký bảo mật
        if (!VNPayHelper::verifyResponse($params)) {
            echo json_encode(['RspCode' => '97', 'Message' => 'Invalid signature']);
            exit;
        }

        $txnRef = $params['vnp_TxnRef'] ?? '';
        $amountReceived = isset($params['vnp_Amount']) ? (float)$params['vnp_Amount'] / 100 : 0.00;
        $responseCode = $params['vnp_ResponseCode'] ?? '';

        $db = new Database();
        $conn = $db->getConnection();

        // 2. Kiểm tra giao dịch tồn tại
        $sqlTxn = "SELECT * FROM payment_transactions WHERE transaction_ref = :txn_ref LIMIT 1";
        $stmtTxn = $conn->prepare($sqlTxn);
        $stmtTxn->execute([':txn_ref' => $txnRef]);
        $txn = $stmtTxn->fetch();

        if (!$txn) {
            echo json_encode(['RspCode' => '01', 'Message' => 'Order not found']);
            exit;
        }

        $invoiceId = $txn['invoice_id'];
        $invoice = $this->invoiceModel->findById($invoiceId);
        if (!$invoice) {
            echo json_encode(['RspCode' => '01', 'Message' => 'Invoice not found']);
            exit;
        }

        // 3. Kiểm tra số tiền khớp
        if (abs($amountReceived - (float)$invoice['final_amount']) > 0.01) {
            echo json_encode(['RspCode' => '04', 'Message' => 'Invalid amount']);
            exit;
        }

        // 4. Xử lý Idempotent
        if ($txn['status'] === 'success' && $invoice['status'] === 'paid') {
            echo json_encode(['RspCode' => '02', 'Message' => 'Order already confirmed']);
            exit;
        }

        try {
            $conn->beginTransaction();

            $conn->prepare("SELECT id FROM payment_transactions WHERE id = :id FOR UPDATE")
                 ->execute([':id' => $txn['id']]);
            $conn->prepare("SELECT id FROM invoices WHERE id = :id FOR UPDATE")
                 ->execute([':id' => $invoiceId]);

            if ($responseCode === '00') {
                $conn->prepare("UPDATE payment_transactions 
                                SET status = 'success', paid_at = NOW(), response_payload = :payload 
                                WHERE id = :id")
                     ->execute([
                         ':payload' => json_encode($params),
                         ':id'      => $txn['id']
                     ]);

                $conn->prepare("UPDATE invoices 
                                SET status = 'paid', payment_method = 'vnpay', 
                                    vnpay_txn_ref = :vnp_ref, 
                                    vnpay_transaction_no = :vnp_no, 
                                    vnpay_response_code = :vnp_code 
                                WHERE id = :id")
                     ->execute([
                         ':vnp_ref'  => $txnRef,
                         ':vnp_no'   => $params['vnp_TransactionNo'] ?? null,
                         ':vnp_code' => $responseCode,
                         ':id'       => $invoiceId
                     ]);

                $patientUser = $conn->query("SELECT user_id FROM patients WHERE id = {$txn['patient_id']}")->fetch();
                $userId = $patientUser['user_id'] ?? null;

                if ($userId) {
                    $this->notificationModel->create(
                        $userId,
                        'Thanh toán thành công',
                        "Hóa đơn mã INV-" . str_pad($invoiceId, 6, '0', STR_PAD_LEFT) . " trị giá " . number_format($invoice['final_amount']) . " VND đã được thanh toán thành công qua cổng VNPay.",
                        'payment'
                    );
                }

                $conn->commit();
                AuditLog::logUpdate('invoices', $invoiceId, ['status' => 'pending'], ['status' => 'paid'], $userId);

                echo json_encode(['RspCode' => '00', 'Message' => 'Confirm success']);
                exit;
            } else {
                $conn->prepare("UPDATE payment_transactions 
                                SET status = 'failed', response_payload = :payload 
                                WHERE id = :id")
                     ->execute([
                         ':payload' => json_encode($params),
                         ':id'      => $txn['id']
                     ]);
                $conn->commit();

                echo json_encode(['RspCode' => '00', 'Message' => 'Confirm success (Failed transaction recorded)']);
                exit;
            }

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("VNPay IPN handler error: " . $e->getMessage());
            echo json_encode(['RspCode' => '99', 'Message' => 'System error']);
            exit;
        }
    }
}
