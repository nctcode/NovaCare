<?php
/**
 * AuditLogController - Quản lý xem nhật ký hệ thống
 */
require_once __DIR__ . '/../helpers/AuditLog.php';

class AuditLogController {
    
    public function __construct() {
        // Chỉ Admin và Director mới được xem Audit Log
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'director'])) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    public function index() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;

        // Thu thập bộ lọc từ GET params
        $filters = [
            'log_type'   => trim($_GET['log_type'] ?? ''),
            'action'     => trim($_GET['action'] ?? ''),
            'table_name' => trim($_GET['table_name'] ?? ''),
            'user_name'  => trim($_GET['user_name'] ?? ''),
            'date_from'  => trim($_GET['date_from'] ?? ''),
            'date_to'    => trim($_GET['date_to'] ?? ''),
        ];
        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, fn($v) => $v !== '');

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $totalLogs  = AuditLog::count($filters);
        $totalPages = (int)ceil($totalLogs / $limit);
        $logs       = AuditLog::getLogs($limit, $offset, $filters);

        // Dữ liệu cho các dropdown bộ lọc
        $availableLogTypes = ['auth' => 'Xác thực (Auth)', 'data_change' => 'Thay đổi dữ liệu (Data)', 'system' => 'Hệ thống (System)'];
        $availableActions = ['LOGIN', 'INSERT', 'UPDATE', 'DELETE', 'LOGOUT'];
        $availableTables  = AuditLog::getDistinctTables();

        require_once __DIR__ . '/../views/audit_logs/index.php';
    }
}
