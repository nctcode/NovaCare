import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class DoctorsNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _doctors = [];
  List<dynamic> get doctors => _doctors;

  String _sortType = 'name_asc'; // 'name_asc', 'name_desc', 'exp_desc'
  String get sortType => _sortType;

  DoctorsNotifier(this._apiClient);

  void setSortType(String type) {
    _sortType = type;
    _sortLocalList();
    notifyListeners();
  }

  void _sortLocalList() {
    if (_doctors.isEmpty) return;
    if (_sortType == 'name_asc') {
      _doctors.sort((a, b) {
        final nameA = (a['name'] ?? '').toString().toLowerCase();
        final nameB = (b['name'] ?? '').toString().toLowerCase();
        return nameA.compareTo(nameB);
      });
    } else if (_sortType == 'name_desc') {
      _doctors.sort((a, b) {
        final nameA = (a['name'] ?? '').toString().toLowerCase();
        final nameB = (b['name'] ?? '').toString().toLowerCase();
        return nameB.compareTo(nameA);
      });
    } else if (_sortType == 'exp_desc') {
      _doctors.sort((a, b) {
        final expA = int.tryParse(a['experience_years']?.toString() ?? '0') ?? 0;
        final expB = int.tryParse(b['experience_years']?.toString() ?? '0') ?? 0;
        return expB.compareTo(expA);
      });
    }
  }

  Future<void> loadDoctors({int? departmentId, String? keyword}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final Map<String, dynamic> params = {};
      if (departmentId != null) {
        params['department_id'] = departmentId;
      }
      if (keyword != null && keyword.trim().isNotEmpty) {
        params['keyword'] = keyword.trim();
      }

      final response = await _apiClient.dio.get(
        ApiConstants.doctors,
        queryParameters: params,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _doctors = List.from(response.data['data'] ?? []);
        _sortLocalList();
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải danh sách bác sĩ.';
      _isLoading = false;
      notifyListeners();
    }
  }
}
