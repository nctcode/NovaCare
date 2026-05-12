<?php
/**
 * AuditLogController - Quản lý xem nhật ký hệ thống
 */
require_once __DIR__ . '/../helpers/AuditLog.php';

class AuditLogController {
    
    public function __construct() {
        // Chỉ Admin mới được xem Audit Log
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    public function index() {
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $totalLogs = AuditLog::count();
        $totalPages = ceil($totalLogs / $limit);
        
        $logs = AuditLog::getLogs($limit, $offset);
        
        require_once __DIR__ . '/../views/audit_logs/index.php';
    }
}
