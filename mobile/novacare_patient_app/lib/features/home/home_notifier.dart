import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class HomeNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  Map<String, dynamic>? _userMe;
  Map<String, dynamic>? get userMe => _userMe;

  Map<String, dynamic>? _todayTicket;
  Map<String, dynamic>? get todayTicket => _todayTicket;

  int _pendingInvoicesCount = 0;
  int get pendingInvoicesCount => _pendingInvoicesCount;

  int _unreadNotificationsCount = 0;
  int get unreadNotificationsCount => _unreadNotificationsCount;

  List<dynamic> _upcomingAppointments = [];
  List<dynamic> get upcomingAppointments => _upcomingAppointments;

  List<dynamic> _pendingInvoices = [];
  List<dynamic> get pendingInvoices => _pendingInvoices;

  List<dynamic> _recentNotifications = [];
  List<dynamic> get recentNotifications => _recentNotifications;

  HomeNotifier(this._apiClient);

  Future<void> loadDashboardData() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      // 1. Gọi /me lấy thông tin user đăng nhập
      final resMe = await _apiClient.dio.get(ApiConstants.me);
      if (resMe.statusCode == 200 && resMe.data['success'] == true) {
        _userMe = resMe.data['data'];
      }

      // 2. Gọi /queue/my-ticket lấy số thứ tự hôm nay
      final resTicket = await _apiClient.dio.get(ApiConstants.myTicket);
      if (resTicket.statusCode == 200 && resTicket.data['success'] == true) {
        _todayTicket = resTicket.data['data'];
      }

      // 3. Gọi /invoices/my?status=pending lấy số hóa đơn chờ thanh toán và danh sách
      final resInvoices = await _apiClient.dio.get(
        ApiConstants.invoices,
        queryParameters: {'status': 'pending'},
      );
      if (resInvoices.statusCode == 200 && resInvoices.data['success'] == true) {
        _pendingInvoices = resInvoices.data['data'] ?? [];
        _pendingInvoicesCount = _pendingInvoices.length;
      }

      // 4. Gọi /notifications/unread-count lấy số thông báo chưa đọc
      final resUnread = await _apiClient.dio.get(ApiConstants.unreadCount);
      if (resUnread.statusCode == 200 && resUnread.data['success'] == true) {
        _unreadNotificationsCount = resUnread.data['data']['unread_count'] ?? 0;
      }

      // 5. Tải danh sách lịch hẹn của tôi và lọc lấy lịch hẹn sắp tới (pending hoặc confirmed)
      final resAppts = await _apiClient.dio.get(ApiConstants.myAppointments);
      if (resAppts.statusCode == 200 && resAppts.data['success'] == true) {
        final List list = resAppts.data['data'] ?? [];
        _upcomingAppointments = list.where((a) {
          final status = a['status'];
          return status == 'pending' || status == 'confirmed';
        }).toList();
      }

      // 6. Tải các thông báo gần nhất
      final resNotifs = await _apiClient.dio.get(ApiConstants.notifications);
      if (resNotifs.statusCode == 200 && resNotifs.data['success'] == true) {
        final List list = resNotifs.data['data'] ?? [];
        _recentNotifications = list.take(3).toList();
      }

      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Lỗi không thể tải dữ liệu Dashboard.';
      _isLoading = false;
      notifyListeners();
    }
  }
}
