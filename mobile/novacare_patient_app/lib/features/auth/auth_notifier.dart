import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';
import '../../core/storage/secure_storage_service.dart';

class AuthNotifier extends ChangeNotifier {
  final ApiClient _apiClient;
  final SecureStorageService _storage = SecureStorageService();

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  bool _isAuthenticated = false;
  bool get isAuthenticated => _isAuthenticated;

  Map<String, String?> _userInfo = {};
  Map<String, String?> get userInfo => _userInfo;

  AuthNotifier(this._apiClient);

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }

  // Đăng nhập
  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post(
        ApiConstants.login,
        data: {
          'email': email,
          'password': password,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final access = data['access_token'];
        final refresh = data['refresh_token'];
        final user = data['user'];

        await _storage.saveAccessToken(access);
        await _storage.saveRefreshToken(refresh);
        await _storage.saveUserInfo(
          userId: user['id'],
          name: user['name'],
          email: user['email'],
        );

        _isAuthenticated = true;
        _userInfo = {
          'id': user['id'].toString(),
          'name': user['name'],
          'email': user['email'],
        };
        _isLoading = false;
        notifyListeners();
        return true;
      }
      throw ApiException(message: response.data['message'] ?? 'Đăng nhập thất bại.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi không xác định. Vui lòng thử lại.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Đăng ký
  Future<bool> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String confirmPassword,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post(
        ApiConstants.register,
        data: {
          'name': name,
          'email': email,
          'phone': phone,
          'password': password,
          'confirm_password': confirmPassword,
        },
      );

      if (response.statusCode == 201 && response.data['success'] == true) {
        final data = response.data['data'];
        final access = data['access_token'];
        final refresh = data['refresh_token'];
        final user = data['user'];

        await _storage.saveAccessToken(access);
        await _storage.saveRefreshToken(refresh);
        await _storage.saveUserInfo(
          userId: user['id'],
          name: user['name'],
          email: user['email'],
        );

        _isAuthenticated = true;
        _userInfo = {
          'id': user['id'].toString(),
          'name': user['name'],
          'email': user['email'],
        };
        _isLoading = false;
        notifyListeners();
        return true;
      }
      throw ApiException(message: response.data['message'] ?? 'Đăng ký tài khoản thất bại.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi không xác định. Vui lòng thử lại.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Đăng xuất
  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    try {
      final refreshToken = await _storage.getRefreshToken();
      if (refreshToken != null) {
        // Gọi API logout gửi kèm refresh token
        await _apiClient.dio.post(
          ApiConstants.logout,
          data: {'refresh_token': refreshToken},
        );
      }
    } catch (e) {
      // Bỏ qua lỗi logout của backend để luôn đăng xuất cục bộ sạch sẽ
    } finally {
      await _storage.clearAuthData();
      _isAuthenticated = false;
      _userInfo = {};
      _isLoading = false;
      notifyListeners();
    }
  }

  // Kiểm tra trạng thái đã đăng nhập chưa khi mở app
  Future<void> checkAuthStatus() async {
    final token = await _storage.getAccessToken();
    if (token != null) {
      _isAuthenticated = true;
      _userInfo = await _storage.getUserInfo();
    } else {
      _isAuthenticated = false;
      _userInfo = {};
    }
    notifyListeners();
  }
}
