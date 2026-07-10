import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import '../constants/api_constants.dart';
import '../storage/secure_storage_service.dart';
import 'api_exception.dart';

class ApiClient {
  final Dio dio;
  final SecureStorageService _storage = SecureStorageService();
  bool _isRefreshing = false;
  final List<void Function(String token)> _refreshQueue = [];

  // Để điều hướng đăng xuất khi token hết hạn
  static final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

  ApiClient()
      : dio = Dio(BaseOptions(
          baseUrl: ApiConstants.baseUrl,
          connectTimeout: const Duration(seconds: 15),
          receiveTimeout: const Duration(seconds: 15),
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        )) {
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        // Kiểm tra xem có custom base URL từ settings lập trình viên không
        final customUrl = await _storage.getCustomBaseUrl();
        if (customUrl != null && customUrl.trim().isNotEmpty) {
          options.baseUrl = customUrl.trim();
        }

        // Gắn Access Token nếu có
        final token = await _storage.getAccessToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (DioException error, handler) async {
        final response = error.response;
        final statusCode = response?.statusCode;

        // Xử lý khi Token hết hạn (401)
        if (statusCode == 401) {
          final requestPath = error.requestOptions.path;
          
          // Tránh lặp vô hạn khi chính API auth bị chặn 401
          if (requestPath != ApiConstants.login &&
              requestPath != ApiConstants.register &&
              requestPath != ApiConstants.refresh) {
            
            if (_isRefreshing) {
              // Nếu đang refresh, thêm request này vào hàng đợi chờ token mới
              _refreshQueue.add((newToken) {
                error.requestOptions.headers['Authorization'] = 'Bearer $newToken';
              });
              // Thực hiện lại request sau khi đợi
              try {
                final retryRes = await _retryRequest(error.requestOptions);
                return handler.resolve(retryRes);
              } catch (e) {
                return handler.next(DioException(
                  requestOptions: error.requestOptions,
                  error: e,
                ));
              }
            }

            _isRefreshing = true;

            try {
              final refreshToken = await _storage.getRefreshToken();
              if (refreshToken == null) {
                throw ApiException(statusCode: 401, message: 'Vui lòng đăng nhập lại.');
              }

              // Gọi API làm mới token bằng một instance Dio sạch khác
              final refreshDio = Dio(BaseOptions(baseUrl: ApiConstants.baseUrl));
              final refreshRes = await refreshDio.post(
                ApiConstants.refresh,
                data: {'refresh_token': refreshToken},
              );

              if (refreshRes.statusCode == 200 && refreshRes.data['success'] == true) {
                final data = refreshRes.data['data'];
                final newAccess = data['access_token'];
                final newRefresh = data['refresh_token'];

                // Lưu token mới
                await _storage.saveAccessToken(newAccess);
                await _storage.saveRefreshToken(newRefresh);

                _isRefreshing = false;

                // Chạy toàn bộ hàng đợi request đang chờ
                for (final callback in _refreshQueue) {
                  callback(newAccess);
                }
                _refreshQueue.clear();

                // Thực hiện lại request hiện tại
                error.requestOptions.headers['Authorization'] = 'Bearer $newAccess';
                final retryRes = await _retryRequest(error.requestOptions);
                return handler.resolve(retryRes);
              } else {
                throw Exception();
              }
            } catch (e) {
              _isRefreshing = false;
              _refreshQueue.clear();
              // Refresh Token hết hạn hoặc bị revoke -> Đăng xuất
              await _storage.clearAuthData();
              
              // Điều hướng về màn hình Login
              navigatorKey.currentState?.pushNamedAndRemoveUntil('/login', (route) => false);
              
              return handler.next(DioException(
                requestOptions: error.requestOptions,
                error: ApiException(statusCode: 401, message: 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'),
              ));
            }
          }
        }

        // Chuyển đổi lỗi thông thường thành ApiException chuẩn hóa
        final apiException = _mapToApiException(error);
        return handler.next(DioException(
          requestOptions: error.requestOptions,
          error: apiException,
          response: error.response,
        ));
      },
    ));
  }

  // Thực hiện lại request cũ
  Future<Response<dynamic>> _retryRequest(RequestOptions requestOptions) {
    final options = Options(
      method: requestOptions.method,
      headers: requestOptions.headers,
    );
    return dio.request<dynamic>(
      requestOptions.path,
      data: requestOptions.data,
      queryParameters: requestOptions.queryParameters,
      options: options,
    );
  }

  // Chuẩn hóa lỗi API
  ApiException _mapToApiException(DioException error) {
    if (error.type == DioExceptionType.connectionTimeout ||
        error.type == DioExceptionType.receiveTimeout ||
        error.type == DioExceptionType.sendTimeout) {
      return ApiException(message: 'Kết nối mạng quá hạn, vui lòng kiểm tra lại đường truyền.');
    }

    if (error.error is SocketException) {
      return ApiException(message: 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra Wifi/3G.');
    }

    final response = error.response;
    final statusCode = response?.statusCode;
    final data = response?.data;

    String message = 'Đã xảy ra lỗi không xác định.';
    Map<String, dynamic>? errors;

    if (data is Map) {
      message = data['message'] ?? message;
      if (data['errors'] is Map) {
        errors = Map<String, dynamic>.from(data['errors']);
      }
    }

    if (statusCode != null) {
      switch (statusCode) {
        case 400:
          return ApiException(statusCode: 400, message: message, errors: errors);
        case 401:
          return ApiException(statusCode: 401, message: message);
        case 403:
          return ApiException(statusCode: 403, message: 'Bạn không có quyền thực hiện hành động này.');
        case 404:
          return ApiException(statusCode: 404, message: message);
        case 409:
          return ApiException(statusCode: 409, message: message);
        case 422:
          return ApiException(statusCode: 422, message: message, errors: errors);
        case 500:
          return ApiException(statusCode: 500, message: 'Hệ thống đang gặp sự cố. Vui lòng quay lại sau.');
        case 501:
          return ApiException(statusCode: 501, message: message);
        case 503:
          return ApiException(statusCode: 503, message: message);
      }
    }

    return ApiException(statusCode: statusCode, message: message, errors: errors);
  }
}
