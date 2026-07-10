import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'notification_notifier.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  int _currentPage = 1;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  void _loadNotifications({int page = 1}) {
    _currentPage = page;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final notifier = Provider.of<NotificationNotifier>(context, listen: false);
      notifier.loadNotifications(page: _currentPage);
      notifier.loadUnreadCount();
    });
  }

  IconData _getTypeIcon(String type) {
    switch (type.toLowerCase()) {
      case 'appointment':
        return Icons.calendar_month_rounded;
      case 'payment':
        return Icons.payment_rounded;
      case 'ai_alert':
        return Icons.warning_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  Color _getTypeColor(String type) {
    switch (type.toLowerCase()) {
      case 'appointment':
        return AppTheme.primaryColor;
      case 'payment':
        return Colors.green;
      case 'ai_alert':
        return AppTheme.errorColor;
      default:
        return Colors.blueGrey;
    }
  }

  String _formatDateTime(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('HH:mm - dd/MM/yyyy').format(parsed);
  }

  void _markAllRead() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.done_all_rounded, color: AppTheme.primaryColor),
              SizedBox(width: 8),
              Text('Đánh dấu đọc tất cả'),
            ],
          ),
          content: const Text('Bạn có chắc chắn muốn đánh dấu toàn bộ thông báo là đã đọc không?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('HỦY BỎ'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(context, true),
              child: const Text('ĐỒNG Ý'),
            ),
          ],
        );
      },
    );

    if (confirmed == true) {
      if (!mounted) return;
      final notifier = Provider.of<NotificationNotifier>(context, listen: false);
      final success = await notifier.markAllAsRead();
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Đã đánh dấu tất cả thông báo là đã đọc.'),
            backgroundColor: Colors.green,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<NotificationNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Thông Báo Của Tôi'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          if (notifier.notifications.isNotEmpty)
            TextButton.icon(
              style: TextButton.styleFrom(foregroundColor: Colors.white),
              onPressed: _markAllRead,
              icon: const Icon(Icons.done_all_rounded, size: 18),
              label: const Text('Đọc tất cả', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
            ),
        ],
      ),
      body: RefreshIndicator(
        color: AppTheme.primaryColor,
        onRefresh: () async => _loadNotifications(page: _currentPage),
        child: notifier.isLoading && notifier.notifications.isEmpty
            ? const LoadingWidget(message: 'Đang tải danh sách thông báo...')
            : notifier.errorMessage != null
                ? ErrorView(message: notifier.errorMessage!, onRetry: () => _loadNotifications(page: _currentPage))
                : notifier.notifications.isEmpty
                    ? const SingleChildScrollView(
                        physics: AlwaysScrollableScrollPhysics(),
                        child: Padding(
                          padding: EdgeInsets.all(40.0),
                          child: EmptyState(
                            message: 'Bạn chưa có thông báo nào.',
                            icon: Icons.notifications_none_rounded,
                          ),
                        ),
                      )
                    : Column(
                        children: [
                          Expanded(
                            child: ListView.builder(
                              physics: const AlwaysScrollableScrollPhysics(),
                              padding: const EdgeInsets.all(12),
                              itemCount: notifier.notifications.length,
                              itemBuilder: (context, index) {
                                final n = notifier.notifications[index];
                                final id = n['id'] as int;
                                final title = n['title'] ?? 'Thông báo';
                                final msg = n['message'] ?? '';
                                final type = n['type'] ?? 'general';
                                final isRead = n['is_read'] == true || n['is_read'] == 1;
                                final dateStr = n['created_at'] ?? '';

                                return Card(
                                  margin: const EdgeInsets.symmetric(vertical: 6),
                                  elevation: 0.5,
                                  // unread: nền xanh nhạt, read: trắng
                                  color: isRead ? Colors.white : const Color(0xffe3f2fd).withOpacity(0.4),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    side: isRead 
                                        ? BorderSide(color: Colors.grey.shade200) 
                                        : const BorderSide(color: AppTheme.primaryColor, width: 0.8),
                                  ),
                                  child: ListTile(
                                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                                    leading: CircleAvatar(
                                      backgroundColor: _getTypeColor(type).withOpacity(0.1),
                                      radius: 20,
                                      child: Icon(_getTypeIcon(type), color: _getTypeColor(type), size: 20),
                                    ),
                                    title: Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: Text(
                                            title,
                                            style: TextStyle(
                                              fontWeight: isRead ? FontWeight.normal : FontWeight.bold,
                                              fontSize: 14,
                                              color: Colors.black87,
                                            ),
                                          ),
                                        ),
                                        if (!isRead)
                                          Container(
                                            width: 8,
                                            height: 8,
                                            decoration: const BoxDecoration(
                                              color: AppTheme.primaryColor,
                                              shape: BoxShape.circle,
                                            ),
                                          ),
                                      ],
                                    ),
                                    subtitle: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const SizedBox(height: 6),
                                        Text(
                                          msg,
                                          style: TextStyle(
                                            color: isRead ? Colors.black54 : Colors.black87,
                                            fontSize: 12.5,
                                            fontWeight: isRead ? FontWeight.normal : FontWeight.w500,
                                            height: 1.3,
                                          ),
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          _formatDateTime(dateStr),
                                          style: TextStyle(color: Colors.grey.shade400, fontSize: 10),
                                        ),
                                      ],
                                    ),
                                    onTap: () {
                                      // Đánh dấu là đã đọc khi bấm vào
                                      if (!isRead) {
                                        notifier.markAsRead(id);
                                      }
                                    },
                                  ),
                                );
                              },
                            ),
                          ),
                          if (notifier.totalPages > 1)
                            Container(
                              padding: const EdgeInsets.symmetric(vertical: 8),
                              color: Colors.white,
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  IconButton(
                                    icon: const Icon(Icons.arrow_back_ios_rounded, size: 18),
                                    onPressed: _currentPage > 1 ? () => _loadNotifications(page: _currentPage - 1) : null,
                                  ),
                                  Text('Trang $_currentPage / ${notifier.totalPages}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                  IconButton(
                                    icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
                                    onPressed: _currentPage < notifier.totalPages ? () => _loadNotifications(page: _currentPage + 1) : null,
                                  ),
                                ],
                              ),
                            ),
                        ],
                      ),
      ),
    );
  }
}
