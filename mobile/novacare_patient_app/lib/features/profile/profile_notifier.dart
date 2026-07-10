import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class ProfileNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  Map<String, dynamic>? _profile;
  Map<String, dynamic>? get profile => _profile;

  ProfileNotifier(this._apiClient);

  // Tải thông tin hồ sơ bệnh nhân
  Future<void> loadProfile() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(ApiConstants.profile);
      if (response.statusCode == 200 && response.data['success'] == true) {
        _profile = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải hồ sơ bệnh nhân.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Cập nhật thông tin hồ sơ
  Future<bool> updateProfile({
    required String name,
    required String phone,
    required String? dateOfBirth,
    required String? gender,
    required String? address,
    required String? bloodType,
    required String? emergencyContact,
    required String? insuranceNumber,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.put(
        ApiConstants.profile,
        data: {
          'name': name,
          'phone': phone,
          'date_of_birth': dateOfBirth,
          'gender': gender,
          'address': address,
          'blood_type': bloodType,
          'emergency_contact': emergencyContact,
          'insurance_number': insuranceNumber,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _profile = response.data['data'];
        _isLoading = false;
        notifyListeners();
        return true;
      }
      throw ApiException(message: response.data['message'] ?? 'Cập nhật thất bại.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi khi cập nhật hồ sơ.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
