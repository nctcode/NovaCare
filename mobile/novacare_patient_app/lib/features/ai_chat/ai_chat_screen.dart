import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import 'ai_chat_notifier.dart';

class AIChatScreen extends StatefulWidget {
  const AIChatScreen({super.key});

  @override
  State<AIChatScreen> createState() => _AIChatScreenState();
}

class _AIChatScreenState extends State<AIChatScreen> {
  final _textController = TextEditingController();
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  void _loadHistory() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AIChatNotifier>(context, listen: false).loadChatHistory().then((_) {
        _scrollToBottom();
      });
    });
  }

  @override
  void dispose() {
    _textController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollToBottom() {
    if (_scrollController.hasClients) {
      Future.delayed(const Duration(milliseconds: 200), () {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      });
    }
  }

  void _sendMessage() async {
    final text = _textController.text.trim();
    if (text.isEmpty) return;

    _textController.clear();
    final notifier = Provider.of<AIChatNotifier>(context, listen: false);
    
    _scrollToBottom();
    final success = await notifier.sendMessage(text);
    
    if (success) {
      _scrollToBottom();
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(notifier.errorMessage ?? 'Không thể gửi tin nhắn.'),
            backgroundColor: AppTheme.errorColor,
          ),
        );
      }
    }
  }

  void _clearChat() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.delete_sweep_rounded, color: AppTheme.errorColor),
              SizedBox(width: 8),
              Text('Xóa lịch sử chat'),
            ],
          ),
          content: const Text(
            'Bạn có chắc chắn muốn xóa toàn bộ lịch sử cuộc trò chuyện triệu chứng này không? Hành động này sẽ thực hiện xóa mềm.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('QUAY LẠI'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.errorColor),
              onPressed: () => Navigator.pop(context, true),
              child: const Text('XÓA SẠCH'),
            ),
          ],
        );
      },
    );

    if (confirmed == true) {
      if (!mounted) return;
      final success = await Provider.of<AIChatNotifier>(context, listen: false).clearChatHistory();
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Đã xóa sạch lịch sử cuộc trò chuyện AI.'),
            backgroundColor: Colors.green,
          ),
        );
      }
    }
  }

  String _formatTime(dynamic dateStr) {
    if (dateStr == null) return '';
    final parsed = DateTime.tryParse(dateStr.toString());
    if (parsed == null) return '';
    return DateFormat('HH:mm').format(parsed);
  }

  Widget _buildQuickPrompts() {
    final prompts = ['Tôi bị đau đầu 🤕', 'Tôi bị sốt 🌡️', 'Tôi bị đau ngực 💔', 'Tôi bị ho 😷'];
    return Container(
      height: 38,
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: prompts.length,
        itemBuilder: (context, index) {
          final prompt = prompts[index];
          return Padding(
            padding: const EdgeInsets.only(right: 8.0),
            child: ActionChip(
              elevation: 0.5,
              label: Text(
                prompt,
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500, color: Colors.black87),
              ),
              onPressed: () {
                // Tách emoji để gửi raw text hoặc gửi kèm emoji đều được
                _textController.text = prompt.split(' ')[0];
                _sendMessage();
              },
              backgroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
                side: BorderSide(color: Colors.grey.shade300),
              ),
            ),
          );
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<AIChatNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      appBar: AppBar(
        title: const Text('Trợ Lý Y Tế AI'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.delete_outline_rounded),
            onPressed: _clearChat,
          ),
        ],
      ),
      body: Column(
        children: [
          // 1. Cảnh báo khẩn cấp (Emergency Banner) đỏ nổi bật
          if (notifier.highUrgencyAlert != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: const BoxDecoration(
                color: Color(0xffffebee),
                border: Border(bottom: BorderSide(color: Color(0xffef9a9a))),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(Icons.warning_rounded, color: AppTheme.errorColor, size: 28),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      notifier.highUrgencyAlert!,
                      style: const TextStyle(
                        color: Color(0xffc62828),
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            ),

          // 2. Vùng hội thoại
          Expanded(
            child: notifier.isLoading && notifier.messages.isEmpty
                ? const LoadingWidget(message: 'Đang tải lịch sử cuộc trò chuyện...')
                : notifier.messages.isEmpty
                    ? Center(
                        child: Padding(
                          padding: const EdgeInsets.all(32.0),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              CircleAvatar(
                                radius: 36,
                                backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
                                child: const Icon(Icons.smart_toy_rounded, size: 40, color: AppTheme.primaryColor),
                              ),
                              const SizedBox(height: 16),
                              const Text(
                                'Hãy mô tả triệu chứng của bạn bên dưới.',
                                style: TextStyle(color: Colors.black87, fontSize: 16, fontWeight: FontWeight.bold),
                                textAlign: TextAlign.center,
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'Ví dụ: "Tôi bị đau ngực nhẹ khi chạy bộ", "Tôi bị sốt cao kèm đau đầu". Trợ lý AI sẽ gợi ý hướng xử lý và chuyên khoa khám phù hợp.',
                                style: TextStyle(color: Colors.grey.shade600, fontSize: 13, height: 1.4),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      )
                    : ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        itemCount: notifier.messages.length,
                        itemBuilder: (context, index) {
                          final msg = notifier.messages[index];
                          final sender = msg['sender'] ?? 'user';
                          final message = msg['message'] ?? '';
                          final timeStr = _formatTime(msg['created_at']);

                          return _buildChatBubble(sender, message, timeStr);
                        },
                      ),
          ),

          // Hiển thị chỉ báo AI đang gõ dưới dạng chat bubble giả lập
          if (notifier.isLoading && notifier.messages.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    CircleAvatar(
                      radius: 14,
                      backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
                      child: const Icon(Icons.smart_toy_rounded, size: 16, color: AppTheme.primaryColor),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          SizedBox(
                            width: 12,
                            height: 12,
                            child: CircularProgressIndicator(strokeWidth: 1.5, valueColor: AlwaysStoppedAnimation(AppTheme.primaryColor)),
                          ),
                          SizedBox(width: 8),
                          Text(
                            'Trợ lý AI đang phân tích...',
                            style: TextStyle(fontSize: 12, color: Colors.black54),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),

          // Sticky Disclaimer y khoa phía trên ô nhập liệu
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
            color: Colors.amber.shade50,
            child: Row(
              children: [
                Icon(Icons.info_outline_rounded, color: Colors.amber.shade800, size: 16),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'AI chỉ hỗ trợ tư vấn tham khảo, không thay thế chẩn đoán y khoa chính thức.',
                    style: TextStyle(fontSize: 11, fontStyle: FontStyle.italic, color: Colors.amber.shade900),
                  ),
                ),
              ],
            ),
          ),

          // 3. Gợi ý câu hỏi nhanh
          Container(
            color: Colors.white,
            padding: const EdgeInsets.only(top: 8),
            child: _buildQuickPrompts(),
          ),

          // 4. Vùng nhập tin nhắn
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            decoration: const BoxDecoration(
              color: Colors.white,
              border: Border(top: BorderSide(color: Color(0xffe0e0e0))),
            ),
            child: SafeArea(
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _textController,
                      decoration: InputDecoration(
                        hintText: 'Mô tả triệu chứng bệnh...',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(24),
                          borderSide: BorderSide.none,
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(24),
                          borderSide: BorderSide.none,
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(24),
                          borderSide: BorderSide.none,
                        ),
                        filled: true,
                        fillColor: Colors.grey.shade100,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
                      ),
                      onSubmitted: (_) => _sendMessage(),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    style: IconButton.styleFrom(
                      backgroundColor: AppTheme.primaryColor,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.all(12),
                    ),
                    icon: const Icon(Icons.send_rounded, size: 20),
                    onPressed: _sendMessage,
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildChatBubble(String sender, String text, String timeStr) {
    final isUser = sender == 'user';
    final isDisclaimer = sender == 'assistant_disclaimer';

    if (isDisclaimer) {
      return Container(
        margin: const EdgeInsets.only(bottom: 12, left: 32, right: 32),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.amber.shade50,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.amber.shade200),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(Icons.info_outline_rounded, color: Colors.amber.shade800, size: 18),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                text,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.amber.shade900,
                  fontStyle: FontStyle.italic,
                  height: 1.4,
                ),
              ),
            ),
          ],
        ),
      );
    }

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        mainAxisAlignment: isUser ? MainAxisAlignment.end : MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (!isUser) ...[
            CircleAvatar(
              radius: 14,
              backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
              child: const Icon(Icons.smart_toy_rounded, size: 16, color: AppTheme.primaryColor),
            ),
            const SizedBox(width: 8),
          ],
          Flexible(
            child: Column(
              crossAxisAlignment: isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(
                    color: isUser ? AppTheme.primaryColor : Colors.white,
                    borderRadius: BorderRadius.only(
                      topLeft: const Radius.circular(16),
                      topRight: const Radius.circular(16),
                      bottomLeft: isUser ? const Radius.circular(16) : const Radius.circular(2),
                      bottomRight: isUser ? const Radius.circular(2) : const Radius.circular(16),
                    ),
                    border: isUser ? null : Border.all(color: Colors.grey.shade200),
                  ),
                  child: Text(
                    text,
                    style: TextStyle(
                      color: isUser ? Colors.white : Colors.black87,
                      fontSize: 14,
                      height: 1.4,
                    ),
                  ),
                ),
                if (timeStr.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 4.0),
                    child: Text(
                      timeStr,
                      style: TextStyle(fontSize: 10, color: Colors.grey.shade500),
                    ),
                  ),
                ],
              ],
            ),
          ),
          if (isUser) ...[
            const SizedBox(width: 8),
            CircleAvatar(
              radius: 14,
              backgroundColor: Colors.grey.shade300,
              child: const Icon(Icons.person_rounded, size: 16, color: Colors.black54),
            ),
          ],
        ],
      ),
    );
  }
}
