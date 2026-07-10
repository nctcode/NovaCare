import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class DepartmentsNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _departments = [];
  List<dynamic> get departments => _departments;

  DepartmentsNotifier(this._apiClient);

  Future<void> loadDepartments() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(ApiConstants.departments);
      if (response.statusCode == 200 && response.data['success'] == true) {
        _departments = response.data['data'] ?? [];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải danh sách khoa phòng.';
      _isLoading = false;
      notifyListeners();
    }
  }
}
