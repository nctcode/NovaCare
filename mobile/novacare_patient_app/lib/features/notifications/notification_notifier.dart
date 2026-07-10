import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class NotificationNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _notifications = [];
  List<dynamic> get notifications => _notifications;

  int _unreadCount = 0;
  int get unreadCount => _unreadCount;

  int _page = 1;
  int _totalPages = 1;
  int get page => _page;
  int get totalPages => _totalPages;

  NotificationNotifier(this._apiClient);

  // Tải danh sách thông báo của tôi
  Future<void> loadNotifications({bool? isRead, int page = 1}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final Map<String, dynamic> queryParams = {'page': page};
      if (isRead != null) {
        queryParams['is_read'] = isRead ? 1 : 0;
      }

      final response = await _apiClient.dio.get(
        ApiConstants.notifications,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _notifications = response.data['data'] ?? [];
        final meta = response.data['meta'];
        if (meta != null) {
          _page = meta['page'] ?? 1;
          _totalPages = meta['total_pages'] ?? 1;
        }
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải danh sách thông báo.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Đọc số lượng chưa đọc
  Future<void> loadUnreadCount() async {
    try {
      final response = await _apiClient.dio.get(ApiConstants.unreadCount);
      if (response.statusCode == 200 && response.data['success'] == true) {
        _unreadCount = response.data['data']['unread_count'] ?? 0;
        notifyListeners();
      }
    } catch (e) {
      // Đọc ngầm, bỏ qua lỗi
    }
  }

  // Đánh dấu 1 thông báo đã đọc
  Future<bool> markAsRead(int id) async {
    try {
      final response = await _apiClient.dio.post('${ApiConstants.notifications}/$id/read');
      if (response.statusCode == 200 && response.data['success'] == true) {
        // Cập nhật local
        for (var n in _notifications) {
          if (n['id'] == id) {
            if (n['is_read'] == false || n['is_read'] == 0) {
              n['is_read'] = true;
              if (_unreadCount > 0) _unreadCount--;
            }
            break;
          }
        }
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  // Đánh dấu đã đọc tất cả
  Future<bool> markAllAsRead() async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post('${ApiConstants.notifications}/read-all');
      _isLoading = false;

      if (response.statusCode == 200 && response.data['success'] == true) {
        // Cập nhật toàn bộ local
        for (var n in _notifications) {
          n['is_read'] = true;
        }
        _unreadCount = 0;
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
