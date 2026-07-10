import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'queue_notifier.dart';

class QueueTrackingScreen extends StatefulWidget {
  const QueueTrackingScreen({super.key});

  @override
  State<QueueTrackingScreen> createState() => _QueueTrackingScreenState();
}

class _QueueTrackingScreenState extends State<QueueTrackingScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<QueueNotifier>(context, listen: false).startPolling();
    });
  }

  @override
  void dispose() {
    // Ngừng polling khi tắt màn hình
    final notifier = Provider.of<QueueNotifier>(context, listen: false);
    notifier.stopPolling();
    super.dispose();
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'waiting':
        return AppTheme.primaryColor;
      case 'calling':
        return AppTheme.errorColor;
      case 'completed':
        return Colors.green;
      case 'cancelled':
        return Colors.grey;
      default:
        return Colors.black54;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'waiting':
        return 'Đang đợi khám';
      case 'calling':
        return 'Đang gọi vào khám!';
      case 'completed':
        return 'Đã hoàn thành lượt';
      case 'cancelled':
        return 'Đã hủy lượt';
      default:
        return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<QueueNotifier>(context);
    final ticket = notifier.ticket;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Theo Dõi Lượt Khám'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            onPressed: () => notifier.loadTicket(showLoading: true),
          ),
        ],
      ),
      body: notifier.isLoading && ticket == null
          ? const LoadingWidget(message: 'Đang tra cứu số thứ tự hôm nay...')
          : notifier.errorMessage != null
              ? ErrorView(
                  message: notifier.errorMessage!,
                  onRetry: () => notifier.loadTicket(showLoading: true),
                )
              : ticket == null
                  ? EmptyState(
                      message: 'Hôm nay bạn chưa đăng ký số thứ tự khám bệnh nào.',
                      icon: Icons.confirmation_number_outlined,
                      actionText: 'ĐẶT LỊCH HẸN KHÁM',
                      onAction: () {
                        Navigator.pop(context);
                        Navigator.pushNamed(context, '/book-appointment');
                      },
                    )
                  : RefreshIndicator(
                      onRefresh: () => notifier.loadTicket(showLoading: true),
                      child: SingleChildScrollView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            // Card Tiêu đề số thứ tự
                            Card(
                              margin: EdgeInsets.zero,
                              color: Colors.white,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              child: Padding(
                                padding: const EdgeInsets.all(20.0),
                                child: Column(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                                      decoration: BoxDecoration(
                                        color: _getStatusColor(ticket['status'] ?? 'waiting').withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(20),
                                      ),
                                      child: Text(
                                        _getStatusText(ticket['status'] ?? 'waiting'),
                                        style: TextStyle(
                                          color: _getStatusColor(ticket['status'] ?? 'waiting'),
                                          fontWeight: FontWeight.bold,
                                          fontSize: 13,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(height: 16),
                                    Text(
                                      (ticket['ticket_number'] ?? '...').toString(),
                                      style: const TextStyle(
                                        fontSize: 48,
                                        fontWeight: FontWeight.bold,
                                        color: AppTheme.primaryColor,
                                        letterSpacing: 2,
                                      ),
                                    ),
                                    const Text(
                                      'SỐ THỨ TỰ CỦA BẠN',
                                      style: TextStyle(color: Colors.black54, fontSize: 13, fontWeight: FontWeight.bold),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Grid thông tin chi tiết số thứ tự
                            Card(
                              margin: EdgeInsets.zero,
                              color: Colors.white,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              child: Padding(
                                padding: const EdgeInsets.all(20.0),
                                child: Column(
                                  children: [
                                    _buildInfoRow('Số Đang Gọi Khám:', (ticket['current_calling_number'] ?? 'Chưa gọi').toString(), isHighlighted: true),
                                    const Divider(height: 24),
                                    _buildInfoRow('Số Người Chờ Dự Kiến:', '${ticket['estimated_waiting_count'] ?? 0} người'),
                                    const Divider(height: 24),
                                    _buildInfoRow('Phòng Khám:', ticket['room_name'] ?? 'Đang chờ xếp phòng'),
                                    const Divider(height: 24),
                                    _buildInfoRow('Bác Sĩ Phụ Trách:', ticket['doctor_name'] ?? 'Bác sĩ'),
                                    const Divider(height: 24),
                                    _buildInfoRow('Khoa Lâm Sàng:', ticket['department_name'] ?? 'Khoa phòng'),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 24),

                            // Subtext hướng dẫn
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: AppTheme.primaryColor.withOpacity(0.05),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.info_outline_rounded, color: AppTheme.primaryColor, size: 20),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      'Màn hình tự động cập nhật mỗi 30 giây. Vui lòng có mặt trước cửa phòng khám khi ước lượng chờ dưới 3 người.',
                                      style: TextStyle(fontSize: 12, color: Colors.grey.shade800),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isHighlighted = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: const TextStyle(fontSize: 14, color: Colors.black54),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: isHighlighted ? 18 : 14,
            fontWeight: FontWeight.bold,
            color: isHighlighted ? AppTheme.errorColor : Colors.black87,
          ),
        ),
      ],
    );
  }
}
