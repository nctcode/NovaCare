<?php
/**
 * REST API Front Controller - Entry Point cho REST API v1
 */

// Bắt lỗi ngoại lệ toàn cục để luôn trả về JSON thay vì HTML báo lỗi PHP
set_exception_handler(function($e) {
    error_log("API Global Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    require_once __DIR__ . '/core/ApiResponse.php';
    ApiResponse::error('Lỗi hệ thống nội bộ từ máy chủ.', 500);
});

// Load database và biến môi trường env.php
require_once __DIR__ . '/../config/database.php';
$env = require __DIR__ . '/../env.php';

// ==========================================
//  CẤU HÌNH CORS BẢO MẬT
// ==========================================
$allowedOrigins = $env['CORS_ALLOWED_ORIGINS'] ?? ['http://localhost', 'http://127.0.0.1'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// CORS Whitelist check
if (in_array($origin, $allowedOrigins) || preg_match('/^http:\/\/localhost(:\d+)?$/', $origin) || preg_match('/^http:\/\/127\.0\.0\.1(:\d+)?$/', $origin)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Xử lý request OPTIONS pre-flight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ==========================================
//  PHÂN TÍCH ĐƯỜNG DẪN ĐỊNH TUYẾN (ROUTING)
// ==========================================
$requestMethod = $_SERVER['REQUEST_METHOD'];
$routes = require __DIR__ . '/routes.php';

// Trích xuất path tương đối (VD: api/v1/auth/login)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']); // VD: /NovaCare/api hoặc /api

// Chuẩn hóa đường dẫn bằng cách loại bỏ base script path
$path = trim(str_replace($scriptName, '', $requestUri), '/');
$path = 'api/' . $path; // Đưa về dạng 'api/v1/...' để đối chiếu trong routes.php

require_once __DIR__ . '/core/ApiResponse.php';

// Đối chiếu định tuyến
$routeConfig = null;
$pathParams = [];

// 1. Đối chiếu khớp chính xác (cho các route tĩnh)
if (isset($routes[$requestMethod][$path])) {
    $routeConfig = $routes[$requestMethod][$path];
} else {
    // 2. Đối chiếu khớp theo mẫu regex (cho các route động chứa {id} hoặc {invoice_id})
    if (isset($routes[$requestMethod])) {
        foreach ($routes[$requestMethod] as $routePattern => $config) {
            if (strpos($routePattern, '{id}') !== false || strpos($routePattern, '{invoice_id}') !== false) {
                // Thay thế {id} và {invoice_id} bằng (\d+) để chỉ chấp nhận số nguyên
                $patternNormalized = str_replace(['{id}', '{invoice_id}'], '(\d+)', $routePattern);
                $regex = '#^' . $patternNormalized . '$#';
                if (preg_match($regex, $path, $matches)) {
                    $routeConfig = $config;
                    $pathParams[] = (int)$matches[1]; // Lưu trữ ID số nguyên
                    break;
                }
            }
        }
    }
}

if (!$routeConfig) {
    ApiResponse::error("Endpoint hoặc phương thức không được hỗ trợ: $requestMethod /$path", 404);
}

$controllerName = $routeConfig['controller'];
$actionName = $routeConfig['action'];
$middlewares = $routeConfig['middleware'];

// ==========================================
//  XỬ LÝ MIDDLEWARE
// ==========================================
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';

foreach ($middlewares as $middleware) {
    if ($middleware === 'auth') {
        AuthMiddleware::handle();
    } elseif (strpos($middleware, 'role:') === 0) {
        $role = substr($middleware, 5);
        RoleMiddleware::handle($role);
    }
}

// ==========================================
//  KHỞI TẠO CONTROLLER & ĐIỀU PHỐI (DISPATCH)
// ==========================================
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    ApiResponse::error("Không thể tải Controller: $controllerName", 500);
}

require_once $controllerFile;
$controllerInstance = new $controllerName();

if (!method_exists($controllerInstance, $actionName)) {
    ApiResponse::error("Hành động $actionName không tồn tại trong Controller $controllerName", 500);
}

// Thực thi action
if (!empty($pathParams)) {
    $controllerInstance->$actionName(...$pathParams);
} else {
    $controllerInstance->$actionName();
}
