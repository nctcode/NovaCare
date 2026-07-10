<?php
/**
 * JWTHelper - Xử lý tạo và giải mã Access Token (JWT), sinh và băm Refresh Token.
 */
class JWTHelper {

    private static $secretKey = null;
    private static $refreshSecretKey = null;
    private static $alg = 'HS256';

    /**
     * Khởi tạo khóa bí mật từ env.php
     */
    private static function initKeys() {
        if (self::$secretKey === null) {
            $env = require __DIR__ . '/../../env.php';
            self::$secretKey = $env['JWT_SECRET'] ?? '';
            self::$refreshSecretKey = $env['JWT_REFRESH_SECRET'] ?? '';

            if (empty(self::$secretKey) || empty(self::$refreshSecretKey)) {
                error_log("CRITICAL SECURITY WARNING: JWT secrets are not configured in env.php.");
            }
        }
    }

    /**
     * Tạo Access Token (JWT) có thời hạn ngắn (mặc định 60 phút)
     */
    public static function generateAccessToken($userId, $role, $email, $name, $expirySeconds = 3600) {
        self::initKeys();

        $header = json_encode(['typ' => 'JWT', 'alg' => self::$alg]);
        
        $iat = time();
        $payload = json_encode([
            'user_id' => (int)$userId,
            'role'    => $role,
            'email'   => $email,
            'name'    => $name,
            'iat'     => $iat,
            'exp'     => $iat + $expirySeconds
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Xác thực và giải mã Access Token (JWT)
     */
    public static function verifyAccessToken($token) {
        self::initKeys();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        // Xác thực chữ ký số
        $validSignature = hash_hmac('sha256', $header . "." . $payload, self::$secretKey, true);
        if (!hash_equals(self::base64UrlDecode($signature), $validSignature)) {
            return false;
        }

        $payloadData = json_decode(self::base64UrlDecode($payload), true);
        if (!$payloadData) {
            return false;
        }

        // Kiểm tra thời hạn hết hạn
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false; // Token hết hạn
        }

        return $payloadData;
    }

    /**
     * Sinh cặp Refresh Token: Chuỗi plain text và hash SHA-256 tương ứng
     */
    public static function generateRefreshToken() {
        // Sinh 32 bytes ngẫu nhiên -> 64 ký tự Hex làm token
        $plainToken = bin2hex(random_bytes(32));
        $hashedToken = self::hashRefreshToken($plainToken);

        return [
            'plain' => $plainToken,
            'hash'  => $hashedToken
        ];
    }

    /**
     * Băm SHA-256 cho Refresh Token để lưu/so sánh trong database
     */
    public static function hashRefreshToken($token) {
        self::initKeys();
        // Kết hợp muối từ JWT_REFRESH_SECRET để băm an toàn
        return hash('sha256', $token . self::$refreshSecretKey);
    }

    private static function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private static function base64UrlDecode($text) {
        $base64 = str_replace(['-', '_'], ['+', '/'], $text);
        $len = strlen($base64) % 4;
        if ($len) {
            $base64 .= str_repeat('=', 4 - $len);
        }
        return base64_decode($base64);
    }
}
