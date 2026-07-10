import 'dart:async';
import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class QueueNotifier extends ChangeNotifier {
  final ApiClient _apiClient;
  Timer? _timer;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  Map<String, dynamic>? _ticket;
  Map<String, dynamic>? get ticket => _ticket;

  QueueNotifier(this._apiClient);

  // Tải lượt khám hôm nay
  Future<void> loadTicket({bool showLoading = true}) async {
    if (showLoading) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      final response = await _apiClient.dio.get(ApiConstants.myTicket);
      if (response.statusCode == 200 && response.data['success'] == true) {
        _ticket = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải thông tin số thứ tự.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Khởi động vòng lặp polling 30 giây/lần
  void startPolling() {
    _timer?.cancel();
    loadTicket(showLoading: true);
    _timer = Timer.periodic(const Duration(seconds: 30), (timer) {
      loadTicket(showLoading: false); // Đọc ngầm, không hiện spinner xoay
    });
  }

  // Hủy vòng lặp polling
  void stopPolling() {
    _timer?.cancel();
    _timer = null;
  }

  @override
  void dispose() {
    stopPolling();
    super.dispose();
  }
}
