import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class RecordNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _records = [];
  List<dynamic> get records => _records;

  Map<String, dynamic>? _selectedRecordDetail;
  Map<String, dynamic>? get selectedRecordDetail => _selectedRecordDetail;

  int _page = 1;
  int _totalPages = 1;
  int get page => _page;
  int get totalPages => _totalPages;

  RecordNotifier(this._apiClient);

  // Tải danh sách bệnh án cá nhân
  Future<void> loadRecords({int page = 1}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(
        ApiConstants.medicalRecords,
        queryParameters: {'page': page},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _records = response.data['data'] ?? [];
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
      _errorMessage = 'Không thể tải danh sách bệnh án.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tải chi tiết bệnh án & sinh hiệu
  Future<void> loadRecordDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get('${ApiConstants.appointments}/../records/$id'); // Đường dẫn /records/{id} khớp normalization
      if (response.statusCode == 200 && response.data['success'] == true) {
        _selectedRecordDetail = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải chi tiết bệnh án.';
      _isLoading = false;
      notifyListeners();
    }
  }
}
