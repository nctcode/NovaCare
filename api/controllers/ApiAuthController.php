<?php
/**
 * ApiAuthController - Controller xử lý đăng ký, đăng nhập, làm mới token, đăng xuất và thông tin tài khoản hiện tại.
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../core/RequestValidator.php';
require_once __DIR__ . '/../core/JWTHelper.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../helpers/Security.php';
require_once __DIR__ . '/../../helpers/AuditLog.php';

class ApiAuthController extends BaseApiController {

    private $userModel;
    private $patientModel;

    public function __construct() {
        $this->userModel = new User();
        $this->patientModel = new Patient();
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login() {
        $input = $this->getJsonInput();

        $validator = new RequestValidator();
        $validator->required($input, ['email', 'password'])
                  ->email($input['email'] ?? '');

        if ($validator->hasErrors()) {
            return $this->sendValidationError($validator->getErrors());
        }

        $email = trim($input['email']);
        $password = trim($input['password']);

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return $this->sendUnauthorized('Email hoặc mật khẩu không chính xác.');
        }

        // Kiểm tra mật khẩu (hash bcrypt)
        if (!Security::verifyPassword($password, $user['password'])) {
            return $this->sendUnauthorized('Email hoặc mật khẩu không chính xác.');
        }

        // Kiểm tra trạng thái tài khoản
        if (($user['status'] ?? 'active') !== 'active') {
            return $this->sendForbidden('Tài khoản của bạn đã bị khóa hoặc tạm dừng.');
        }

        // Sinh cặp Access Token (JWT) và Refresh Token
        $accessToken = JWTHelper::generateAccessToken($user['id'], $user['role'], $user['email'], $user['name']);
        $refreshTokenData = JWTHelper::generateRefreshToken();

        // Lưu Hashed Refresh Token vào database
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $deviceName = trim($input['device_name'] ?? $ua);
        if (empty($deviceName)) {
            $deviceName = 'Unknown Device';
        }
        
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 86400)); // 30 ngày

        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $sql = "INSERT INTO api_refresh_tokens (user_id, refresh_token_hash, device_name, user_agent, ip_address, expires_at) 
                    VALUES (:user_id, :hash, :device_name, :user_agent, :ip, :expires_at)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id'     => $user['id'],
                ':hash'        => $refreshTokenData['hash'],
                ':device_name' => $deviceName,
                ':user_agent'  => $ua,
                ':ip'          => $ip,
                ':expires_at'  => $expiresAt
            ]);
            
            // Ghi nhận Audit Log đăng nhập
            AuditLog::logLogin($user['id'], $user['email']);

            return $this->sendSuccess([
                'access_token'  => $accessToken,
                'refresh_token' => $refreshTokenData['plain'],
                'user' => [
                    'id'    => (int)$user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role'],
                    'phone' => $user['phone']
                ]
            ], 'Đăng nhập thành công');

        } catch (Exception $e) {
            error_log("Login Token Save Error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi máy chủ khi tạo phiên đăng nhập.');
        }
    }

    /**
     * POST /api/v1/auth/register
     */
    public function register() {
        $input = $this->getJsonInput();

        $validator = new RequestValidator();
        $validator->required($input, ['name', 'email', 'phone', 'password', 'confirm_password'])
                  ->email($input['email'] ?? '')
                  ->phone($input['phone'] ?? '')
                  ->minLength($input['password'] ?? '', 6, 'password')
                  ->matches($input['password'] ?? '', $input['confirm_password'] ?? '', 'confirm_password');

        if ($validator->hasErrors()) {
            return $this->sendValidationError($validator->getErrors());
        }

        $email = trim($input['email']);
        $phone = trim($input['phone']);
        $name = trim($input['name']);
        $password = $input['password'];

        // Kiểm tra email tồn tại
        if ($this->userModel->findByEmail($email)) {
            return $this->sendConflict('Email này đã được sử dụng.');
        }

        // Kiểm tra số điện thoại tồn tại
        if ($this->userModel->findByPhone($phone)) {
            return $this->sendConflict('Số điện thoại này đã được sử dụng.');
        }

        // Thực hiện tạo tài khoản và hồ sơ bệnh nhân trong Transaction
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $conn->beginTransaction();

            // Dữ liệu tạo bệnh nhân
            $patientData = [
                'name'            => $name,
                'email'           => $email,
                'phone'           => $phone,
                'password'        => $password,
                'date_of_birth'   => null,
                'gender'          => 'other',
                'address'         => null,
                'blood_type'      => null,
                'medical_history' => null
            ];

            // Gọi model Patient create (hàm tự tạo user + patient)
            $patientId = $this->patientModel->create($patientData);
            
            // Lấy ID user vừa được tạo từ database
            $newUser = $this->userModel->findByEmail($email);
            if (!$newUser) {
                throw new Exception("Không thể tìm thấy tài khoản vừa tạo.");
            }

            $conn->commit();

            // Sinh Access Token và Refresh Token cho user mới đăng ký
            $accessToken = JWTHelper::generateAccessToken($newUser['id'], $newUser['role'], $newUser['email'], $newUser['name']);
            $refreshTokenData = JWTHelper::generateRefreshToken();

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $expiresAt = date('Y-m-d H:i:s', time() + (30 * 86400));

            $sql = "INSERT INTO api_refresh_tokens (user_id, refresh_token_hash, device_name, user_agent, ip_address, expires_at) 
                    VALUES (:user_id, :hash, :device_name, :user_agent, :ip, :expires_at)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id'     => $newUser['id'],
                ':hash'        => $refreshTokenData['hash'],
                ':device_name' => trim($input['device_name'] ?? $ua ?: 'Unknown Device'),
                ':user_agent'  => $ua,
                ':ip'          => $ip,
                ':expires_at'  => $expiresAt
            ]);

            // Ghi nhận Audit Log đăng ký
            AuditLog::logCreate('users', $newUser['id'], ['name' => $name, 'email' => $email], $newUser['id']);

            return $this->sendSuccess([
                'access_token'  => $accessToken,
                'refresh_token' => $refreshTokenData['plain'],
                'user' => [
                    'id'    => (int)$newUser['id'],
                    'name'  => $newUser['name'],
                    'email' => $newUser['email'],
                    'role'  => $newUser['role'],
                    'phone' => $newUser['phone']
                ]
            ], 'Đăng ký tài khoản bệnh nhân thành công', 201);

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Register transaction failed: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi hệ thống khi tạo tài khoản. Vui lòng thử lại sau.');
        }
    }

    /**
     * POST /api/v1/auth/refresh (Refresh Token Rotation)
     */
    public function refresh() {
        $input = $this->getJsonInput();

        if (empty($input['refresh_token'])) {
            return $this->sendError('Thiếu tham số refresh_token.', 400);
        }

        $plainRefreshToken = trim($input['refresh_token']);
        $hashedToken = JWTHelper::hashRefreshToken($plainRefreshToken);

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Tìm refresh token trong database
            $sql = "SELECT * FROM api_refresh_tokens WHERE refresh_token_hash = :hash LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':hash' => $hashedToken]);
            $tokenRecord = $stmt->fetch();

            if (!$tokenRecord) {
                return $this->sendUnauthorized('Phiên đăng nhập không hợp lệ.');
            }

            // Kiểm tra token đã bị thu hồi hoặc hết hạn chưa
            if ($tokenRecord['revoked_at'] !== null) {
                // Bảo mật nâng cao: Nếu phát hiện token đã bị thu hồi lại được sử dụng, có thể là do bị đánh cắp -> Thu hồi toàn bộ token hoạt động của user này
                $sql = "UPDATE api_refresh_tokens SET revoked_at = NOW() WHERE user_id = :uid AND revoked_at IS NULL";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':uid' => $tokenRecord['user_id']]);

                return $this->sendUnauthorized('Phát hiện dấu hiệu xâm nhập. Vui lòng đăng nhập lại.');
            }

            if (strtotime($tokenRecord['expires_at']) < time()) {
                return $this->sendUnauthorized('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            }

            // Lấy thông tin user
            $user = $this->userModel->findById($tokenRecord['user_id']);
            if (!$user || $user['status'] !== 'active') {
                return $this->sendUnauthorized('Tài khoản không hoạt động hoặc không tồn tại.');
            }

            // Tiến hành hủy token cũ và tạo token mới (Refresh Token Rotation)
            $conn->beginTransaction();

            $now = date('Y-m-d H:i:s');
            // Hủy token hiện tại
            $sql = "UPDATE api_refresh_tokens SET revoked_at = :revoked_at, last_used_at = :last_used_at WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':revoked_at'   => $now,
                ':last_used_at' => $now,
                ':id'           => $tokenRecord['id']
            ]);

            // Sinh cặp token mới
            $newAccessToken = JWTHelper::generateAccessToken($user['id'], $user['role'], $user['email'], $user['name']);
            $newRefreshTokenData = JWTHelper::generateRefreshToken();

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $expiresAt = date('Y-m-d H:i:s', time() + (30 * 86400));

            // Lưu token mới và gán replaced_by_token_id
            $sql = "INSERT INTO api_refresh_tokens (user_id, refresh_token_hash, device_name, user_agent, ip_address, expires_at, replaced_by_token_id) 
                    VALUES (:user_id, :hash, :device_name, :user_agent, :ip, :expires_at, :replaced_id)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':user_id'     => $user['id'],
                ':hash'        => $newRefreshTokenData['hash'],
                ':device_name' => $tokenRecord['device_name'],
                ':user_agent'  => $ua,
                ':ip'          => $ip,
                ':expires_at'  => $expiresAt,
                ':replaced_id' => $tokenRecord['id']
            ]);

            $conn->commit();

            return $this->sendSuccess([
                'access_token'  => $newAccessToken,
                'refresh_token' => $newRefreshTokenData['plain']
            ], 'Làm mới token thành công');

        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("Token Rotation Error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi khi làm mới token.');
        }
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout() {
        $input = $this->getJsonInput();

        if (empty($input['refresh_token'])) {
            return $this->sendError('Thiếu tham số refresh_token.', 400);
        }

        $plainRefreshToken = trim($input['refresh_token']);
        $hashedToken = JWTHelper::hashRefreshToken($plainRefreshToken);

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Tìm và thu hồi refresh token tương ứng
            $sql = "SELECT id, user_id FROM api_refresh_tokens WHERE refresh_token_hash = :hash LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':hash' => $hashedToken]);
            $tokenRecord = $stmt->fetch();

            if ($tokenRecord) {
                $sql = "UPDATE api_refresh_tokens SET revoked_at = NOW() WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $tokenRecord['id']]);

                // Ghi nhận Audit Log đăng xuất
                AuditLog::logLogout($tokenRecord['user_id']);
            }

            return $this->sendSuccess(null, 'Đăng xuất thành công');

        } catch (Exception $e) {
            error_log("Logout Error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi máy chủ khi đăng xuất.');
        }
    }

    /**
     * GET /api/v1/me
     */
    public function me() {
        // Lấy thông tin user hiện tại qua context của AuthMiddleware
        $user = AuthMiddleware::$currentUser;

        if (!$user) {
            return $this->sendUnauthorized();
        }

        return $this->sendSuccess([
            'id'    => (int)$user['user_id'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'name'  => $user['name']
        ]);
    }
}
