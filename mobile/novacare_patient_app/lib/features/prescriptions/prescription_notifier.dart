import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class PrescriptionNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _prescriptions = [];
  List<dynamic> get prescriptions => _prescriptions;

  Map<String, dynamic>? _selectedPrescriptionDetail;
  Map<String, dynamic>? get selectedPrescriptionDetail => _selectedPrescriptionDetail;

  int _page = 1;
  int _totalPages = 1;
  int get page => _page;
  int get totalPages => _totalPages;

  PrescriptionNotifier(this._apiClient);

  // Tải danh sách đơn thuốc cá nhân
  Future<void> loadPrescriptions({int page = 1}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(
        ApiConstants.prescriptions,
        queryParameters: {'page': page},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _prescriptions = response.data['data'] ?? [];
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
      _errorMessage = 'Không thể tải danh sách đơn thuốc.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tải chi tiết đơn thuốc biệt dược
  Future<void> loadPrescriptionDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get('${ApiConstants.appointments}/../prescriptions/$id'); // Đường dẫn /prescriptions/{id} khớp normalization
      if (response.statusCode == 200 && response.data['success'] == true) {
        _selectedPrescriptionDetail = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải chi tiết đơn thuốc.';
      _isLoading = false;
      notifyListeners();
    }
  }
}
