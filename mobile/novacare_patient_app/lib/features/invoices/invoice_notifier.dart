import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class InvoiceNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _invoices = [];
  List<dynamic> get invoices => _invoices;

  Map<String, dynamic>? _selectedInvoiceDetail;
  Map<String, dynamic>? get selectedInvoiceDetail => _selectedInvoiceDetail;

  int _page = 1;
  int _totalPages = 1;
  int get page => _page;
  int get totalPages => _totalPages;

  InvoiceNotifier(this._apiClient);

  // Tải danh sách hóa đơn viện phí
  Future<void> loadMyInvoices({String? status, int page = 1}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final Map<String, dynamic> queryParams = {'page': page};
      if (status != null && status != 'all') {
        queryParams['status'] = status;
      }

      final response = await _apiClient.dio.get(
        ApiConstants.invoices,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _invoices = response.data['data'] ?? [];
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
      _errorMessage = 'Không thể tải danh sách hóa đơn.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tải chi tiết hóa đơn
  Future<void> loadInvoiceDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get('${ApiConstants.appointments}/../invoices/$id'); // Đường dẫn /invoices/{id} khớp normalization
      if (response.statusCode == 200 && response.data['success'] == true) {
        _selectedInvoiceDetail = response.data['data'];
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải chi tiết hóa đơn.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Tạo liên kết thanh toán VNPay
  Future<String?> createVNPayPayment(int invoiceId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post(
        ApiConstants.vnpayCreate,
        data: {'invoice_id': invoiceId},
      );

      _isLoading = false;
      notifyListeners();

      if (response.statusCode == 200 && response.data['success'] == true) {
        return response.data['data']['payment_url'];
      }
      throw ApiException(message: response.data['message'] ?? 'Không thể khởi tạo thanh toán.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return null;
    } catch (e) {
      _errorMessage = 'Đã xảy ra lỗi khi tạo cổng thanh toán VNPay.';
      _isLoading = false;
      notifyListeners();
      return null;
    }
  }

  // Kiểm tra trạng thái thanh toán từ backend sau khi hoàn thành giao dịch
  Future<Map<String, dynamic>?> checkPaymentStatus(int invoiceId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(
        '${ApiConstants.baseUrl}/payments/$invoiceId/status',
      );
      _isLoading = false;
      notifyListeners();

      if (response.statusCode == 200 && response.data['success'] == true) {
        final paymentData = response.data['data'];
        // Cập nhật lại trạng thái trong detail nếu đang xem
        if (_selectedInvoiceDetail != null && _selectedInvoiceDetail!['id'] == invoiceId) {
          _selectedInvoiceDetail!['status'] = paymentData['payment_status'];
          _selectedInvoiceDetail!['payment_method'] = paymentData['payment_method'];
        }
        notifyListeners();
        return paymentData;
      }
      return null;
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      return null;
    }
  }
}
