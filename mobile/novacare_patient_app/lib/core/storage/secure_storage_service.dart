import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  final FlutterSecureStorage _storage = const FlutterSecureStorage(
    aOptions: AndroidOptions(
      encryptedSharedPreferences: true,
    ),
  );

  static const String _accessTokenKey = 'access_token';
  static const String _refreshTokenKey = 'refresh_token';
  static const String _userIdKey = 'user_id';
  static const String _userNameKey = 'user_name';
  static const String _userEmailKey = 'user_email';

  // Lưu Access Token
  Future<void> saveAccessToken(String token) async {
    await _storage.write(key: _accessTokenKey, value: token);
  }

  // Đọc Access Token
  Future<String?> getAccessToken() async {
    return await _storage.read(key: _accessTokenKey);
  }

  // Lưu Refresh Token
  Future<void> saveRefreshToken(String token) async {
    await _storage.write(key: _refreshTokenKey, value: token);
  }

  // Đọc Refresh Token
  Future<String?> getRefreshToken() async {
    return await _storage.read(key: _refreshTokenKey);
  }

  // Lưu thông tin User
  Future<void> saveUserInfo({
    required int userId,
    required String name,
    required String email,
  }) async {
    await _storage.write(key: _userIdKey, value: userId.toString());
    await _storage.write(key: _userNameKey, value: name);
    await _storage.write(key: _userEmailKey, value: email);
  }

  // Đọc thông tin User
  Future<Map<String, String?>> getUserInfo() async {
    final userId = await _storage.read(key: _userIdKey);
    final name = await _storage.read(key: _userNameKey);
    final email = await _storage.read(key: _userEmailKey);
    return {
      'id': userId,
      'name': name,
      'email': email,
    };
  }

  // Xóa sạch thông tin đăng nhập
  Future<void> clearAuthData() async {
    await _storage.delete(key: _accessTokenKey);
    await _storage.delete(key: _refreshTokenKey);
    await _storage.delete(key: _userIdKey);
    await _storage.delete(key: _userNameKey);
    await _storage.delete(key: _userEmailKey);
  }

  // Quản lý cấu hình sinh trắc học
  static const String _biometricEnabledKey = 'use_biometric';

  Future<void> saveBiometricEnabled(bool enabled) async {
    await _storage.write(key: _biometricEnabledKey, value: enabled.toString());
  }

  Future<bool> isBiometricEnabled() async {
    final value = await _storage.read(key: _biometricEnabledKey);
    return value == 'true';
  }

  // Quản lý custom Base URL (Developer Mode)
  static const String _customBaseUrlKey = 'custom_base_url';

  Future<void> saveCustomBaseUrl(String? url) async {
    if (url == null || url.trim().isEmpty) {
      await _storage.delete(key: _customBaseUrlKey);
    } else {
      await _storage.write(key: _customBaseUrlKey, value: url.trim());
    }
  }

  Future<String?> getCustomBaseUrl() async {
    return await _storage.read(key: _customBaseUrlKey);
  }
}
