import 'package:flutter/material.dart';
import '../../core/network/api_client.dart';
import '../../core/network/api_exception.dart';
import '../../core/constants/api_constants.dart';

class AIChatNotifier extends ChangeNotifier {
  final ApiClient _apiClient;

  bool _isLoading = false;
  bool get isLoading => _isLoading;

  String? _errorMessage;
  String? get errorMessage => _errorMessage;

  List<dynamic> _messages = [];
  List<dynamic> get messages => _messages;

  // Cảnh báo khẩn cấp từ tin nhắn cuối cùng nhận được
  String? _highUrgencyAlert;
  String? get highUrgencyAlert => _highUrgencyAlert;

  AIChatNotifier(this._apiClient);

  // Tải lịch sử cuộc hội thoại cũ
  Future<void> loadChatHistory() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.get(ApiConstants.aiChatHistory);
      if (response.statusCode == 200 && response.data['success'] == true) {
        // Backend trả về lịch sử (mặc định xếp tăng dần theo thời gian)
        final List list = response.data['data'] ?? [];
        _messages = List.from(list);
        
        // Kiểm tra xem có cảnh báo khẩn cấp nào từ tin nhắn AI gần nhất không
        _checkHighUrgencyAlertFromHistory();
      }
      _isLoading = false;
      notifyListeners();
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Không thể tải lịch sử chat.';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Gửi câu hỏi triệu chứng lên AI
  Future<bool> sendMessage(String text) async {
    // 1. Thêm câu hỏi của user vào danh sách tin nhắn ngay lập tức để tạo UI mượt mà
    final userMsg = {
      'id': DateTime.now().millisecondsSinceEpoch,
      'sender': 'user',
      'message': text,
      'created_at': DateTime.now().toIso8601String(),
    };
    _messages.add(userMsg);
    _highUrgencyAlert = null; // Clear cảnh báo cũ
    notifyListeners();

    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post(
        ApiConstants.aiChat,
        data: {'message': text},
      );

      _isLoading = false;

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final reply = data['reply'];
        final disclaimer = data['disclaimer'];
        final urgency = data['urgency_level'];
        final dept = data['department_suggestion'];

        // Thêm câu trả lời của AI
        final aiMsg = {
          'id': DateTime.now().millisecondsSinceEpoch + 1,
          'sender': 'assistant',
          'message': reply,
          'urgency_level': urgency,
          'department_suggestion': dept,
          'created_at': DateTime.now().toIso8601String(),
        };
        _messages.add(aiMsg);

        // Thêm disclaimer của AI
        if (disclaimer != null) {
          final disMsg = {
            'id': DateTime.now().millisecondsSinceEpoch + 2,
            'sender': 'assistant_disclaimer',
            'message': disclaimer,
            'created_at': DateTime.now().toIso8601String(),
          };
          _messages.add(disMsg);
        }

        // Xử lý kiểm tra mức độ nguy hiểm khẩn cấp (high)
        if (urgency == 'high') {
          _highUrgencyAlert = 'Cảnh báo khẩn cấp: Triệu chứng của bạn ở mức độ nghiêm trọng. Bạn được khuyến nghị đến ngay khoa phòng khám chuyên môn ($dept) hoặc gọi cấp cứu 115!';
        }

        notifyListeners();
        return true;
      }
      throw ApiException(message: 'Không thể nhận phản hồi từ AI.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Lỗi kết nối dịch vụ AI.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Xóa lịch sử trò chuyện (Soft Delete phía Backend & Client)
  Future<bool> clearChatHistory() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.delete(ApiConstants.aiChatHistory);
      if (response.statusCode == 200 && response.data['success'] == true) {
        _messages.clear();
        _highUrgencyAlert = null;
        _isLoading = false;
        notifyListeners();
        return true;
      }
      throw ApiException(message: 'Không thể xóa lịch sử chat.');
    } on ApiException catch (e) {
      _errorMessage = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _errorMessage = 'Gặp sự cố khi xóa lịch sử.';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Kiểm tra cảnh báo khẩn cấp từ tin nhắn cuối cùng trong lịch sử cũ
  void _checkHighUrgencyAlertFromHistory() {
    if (_messages.isEmpty) return;
    
    // Tìm tin nhắn assistant cuối cùng
    for (int i = _messages.length - 1; i >= 0; i--) {
      final msg = _messages[i];
      if (msg['sender'] == 'assistant' && msg['urgency_level'] == 'high') {
        final dept = msg['department_suggestion'] ?? 'Khoa khám bệnh';
        _highUrgencyAlert = 'Cảnh báo khẩn cấp: Triệu chứng của bạn ở mức độ nghiêm trọng. Bạn được khuyến nghị đến ngay khoa phòng khám chuyên môn ($dept) hoặc gọi cấp cứu 115!';
        break;
      }
    }
  }
}
