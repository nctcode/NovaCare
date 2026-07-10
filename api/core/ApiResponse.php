<?php
/**
 * ApiResponse - Chuẩn hóa phản hồi JSON cho toàn bộ REST API
 */
class ApiResponse {

    /**
     * Phản hồi thành công
     */
    public static function success($data = null, $message = 'OK', $statusCode = 200, $meta = new stdClass()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
            'meta'    => $meta
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Phản hồi thất bại
     */
    public static function error($message = 'Đã xảy ra lỗi', $statusCode = 400, $errors = new stdClass(), $meta = new stdClass()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
            'meta'    => $meta
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
