<?php
/**
 * BaseApiController - Lớp cha cho tất cả các API Controller
 */
require_once __DIR__ . '/ApiResponse.php';

class BaseApiController {

    /**
     * Lấy dữ liệu JSON gửi từ body của request
     */
    protected function getJsonInput() {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Phản hồi thành công
     */
    protected function sendSuccess($data = null, $message = 'OK', $statusCode = 200, $meta = new stdClass()) {
        ApiResponse::success($data, $message, $statusCode, $meta);
    }

    /**
     * Phản hồi lỗi
     */
    protected function sendError($message = 'Đã xảy ra lỗi', $statusCode = 400, $errors = new stdClass(), $meta = new stdClass()) {
        ApiResponse::error($message, $statusCode, $errors, $meta);
    }

    /**
     * Phản hồi lỗi dữ liệu không hợp lệ (422)
     */
    protected function sendValidationError($errors) {
        $this->sendError('Dữ liệu không hợp lệ', 422, $errors);
    }

    /**
     * Phản hồi lỗi không có quyền truy cập (401)
     */
    protected function sendUnauthorized($message = 'Chưa xác thực hoặc token không hợp lệ') {
        $this->sendError($message, 401);
    }

    /**
     * Phản hồi lỗi cấm truy cập (403)
     */
    protected function sendForbidden($message = 'Bạn không có quyền truy cập tài nguyên này') {
        $this->sendError($message, 403);
    }

    /**
     * Phản hồi lỗi không tìm thấy tài nguyên (404)
     */
    protected function sendNotFound($message = 'Tài nguyên không tồn tại') {
        $this->sendError($message, 404);
    }

    /**
     * Phản hồi lỗi xung đột (409)
     */
    protected function sendConflict($message = 'Tài nguyên đã tồn tại hoặc bị xung đột') {
        $this->sendError($message, 409);
    }

    /**
     * Phản hồi lỗi hệ thống (500)
     */
    protected function sendServerError($message = 'Lỗi hệ thống nội bộ') {
        $this->sendError($message, 500);
    }
}
