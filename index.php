<?php
/**
 * Entry Point - NovaCare Smart Hospital Management System
 * 
 * Xử lý cả public pages (landing) và authenticated pages (admin dashboard).
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Security.php';
require_once __DIR__ . '/helpers/AuditLog.php';
require_once __DIR__ . '/routes.php';

// Khởi tạo CSRF token cho mọi request
Security::getCsrfToken();

// Lấy page và action từ URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// --- Public pages (không cần login) ---
$publicPages = ['home', 'about', 'services', 'public-doctors', 'contact', 'book-appointment'];

if (in_array($page, $publicPages)) {
    // Load public page controller
    require_once __DIR__ . '/controllers/PublicController.php';
    $controller = new PublicController();
    if (method_exists($controller, $page === 'public-doctors' ? 'doctors' : str_replace('-', '', $page))) {
        $method = $page === 'public-doctors' ? 'doctors' : str_replace('-', '', $page);
        $controller->$method();
    } else {
        $controller->home();
    }
    exit;
}

// --- Auth pages (login, register) ---
if ($page === 'login' || $page === 'register') {
    if (isset($_SESSION['user'])) {
        header('Location: index.php?page=dashboard');
        exit;
    }
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    if ($page === 'register') {
        $controller->register();
    } else {
        $controller->index();
    }
    exit;
}

// --- Kiểm tra đăng nhập cho các page còn lại ---
if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}

// Xử lý logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=home');
    exit;
}

// Tìm controller từ routes
if (isset($routes[$page])) {
    $controllerName = $routes[$page];
    $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            $controller->index();
        }
    } else {
        echo "<h3>Controller không tồn tại: {$controllerName}</h3>";
    }
} else {
    header('Location: index.php?page=home');
    exit;
}
