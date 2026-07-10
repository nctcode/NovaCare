import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class AppointmentNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _myAppointments = [];
  List<dynamic> get myAppointments => _myAppointments;

  Map<String, dynamic>? _selectedAppointmentDetail;
  Map<String, dynamic>? get selectedAppointmentDetail => _selectedAppointmentDetail;

  List<String> _availableSlots = [];
  List<String> get availableSlots => _availableSlots;

  int _page = 1;
  int _totalPages = 1;
  int get page => _page;
  int get totalPages => _totalPages;

  AppointmentNotifier(this._apiClient);

  // Tải danh sách lịch hẹn của tôi
  Future<void> loadMyAppointments({
    String? status,
    String? fromDate,
    String? toDate,
    int page = 1,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final Map<String, dynamic> queryParams = {'page': page};
      if (status != null && status != 'all') {
        queryParams['status'] = status;
      }
      if (fromDate != null) {
        queryParams['from_date'] = fromDate;
      }
      if (toDate != null) {
        queryParams['to_date'] = toDate;
      }

      final response = await _apiClient.dio.get(
        ApiConstants.myAppointments,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _myAppointments = response.data['data'] ?? [];
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
      _errorMessage = 'Không thể tải danh sách lịch khám.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tải chi tiết lịch hẹn
  Future<void> loadAppointmentDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get('${ApiConstants.appointments}/$id');
      if (response.statusCode == 200 && response.data['success'] == true) {
        _selectedAppointmentDetail = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải chi tiết lịch hẹn.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tải slot trống khả dụng
  Future<void> loadAvailableSlots(int doctorId, String date) async {
    _isLoading = true;
    _errorMessage = null;
    _availableSlots = [];
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(
        ApiConstants.availableSlots,
        queryParameters: {
          'doctor_id': doctorId,
          'date': date,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final List slots = response.data['data'] ?? [];
        _availableSlots = slots.map((s) => s.toString()).toList();
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải khung giờ trống.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Đặt lịch hẹn khám bệnh mới
  Future<bool> bookAppointment({
    required int doctorId,
    required int departmentId,
    required String date,
    required String time,
    required String reason,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      // Bắt buộc KHÔNG được gửi trường 'type'
      final response = await _apiClient.dio.post(
        ApiConstants.appointments,
        data: {
          'doctor_id': doctorId,
          'department_id': departmentId,
          'appointment_date': date,
          'appointment_time': time,
          'reason': reason,
        },
      );

      _isLoading = false;
      notifyListeners();
      return (response.statusCode == 201 && response.data['success'] == true);
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi không xác định khi đặt lịch.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Hủy lịch hẹn khám
  Future<bool> cancelAppointment(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post('${ApiConstants.appointments}/$id/cancel');
      _isLoading = false;
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        // Cập nhật lại status trong detail nếu đang xem
        if (_selectedAppointmentDetail != null && _selectedAppointmentDetail!['id'] == id) {
          _selectedAppointmentDetail!['status'] = 'cancelled';
        }
        notifyListeners();
        return true;
      }
      throw ApiException(message: response.data['message'] ?? 'Không thể hủy lịch khám.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi khi hủy lịch.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
