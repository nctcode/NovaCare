import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import 'appointment_notifier.dart';

class AppointmentDetailScreen extends StatefulWidget {
  final int appointmentId;

  const AppointmentDetailScreen({super.key, required this.appointmentId});

  @override
  State<AppointmentDetailScreen> createState() => _AppointmentDetailScreenState();
}

class _AppointmentDetailScreenState extends State<AppointmentDetailScreen> {
  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  void _loadDetail() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppointmentNotifier>(context, listen: false)
          .loadAppointmentDetail(widget.appointmentId);
    });
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
        return Colors.orange;
      case 'confirmed':
        return AppTheme.primaryColor;
      case 'completed':
        return Colors.green;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'pending':
        return 'Chờ xác nhận';
      case 'confirmed':
        return 'Đã xác nhận';
      case 'completed':
        return 'Đã hoàn thành';
      case 'cancelled':
        return 'Đã hủy';
      default:
        return status;
    }
  }

  // Luồng xử lý hủy lịch khám
  void _cancelAppt() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.warning_amber_rounded, color: AppTheme.errorColor),
              SizedBox(width: 8),
              Text('Hủy lịch khám'),
            ],
          ),
          content: const Text(
            'Bạn có chắc chắn muốn hủy lịch hẹn khám bệnh này không? Hành động này không thể hoàn tác.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('QUAY LẠI'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.errorColor),
              onPressed: () => Navigator.pop(context, true),
              child: const Text('HỦY LỊCH'),
            ),
          ],
        );
      },
    );

    if (confirmed == true) {
      if (!mounted) return;
      final notifier = Provider.of<AppointmentNotifier>(context, listen: false);
      final success = await notifier.cancelAppointment(widget.appointmentId);

      if (!mounted) return;

      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Hủy lịch hẹn khám thành công!'),
            backgroundColor: Colors.green,
          ),
        );
        _loadDetail(); // Tải lại chi tiết
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(notifier.errorMessage ?? 'Không thể hủy lịch.'),
            backgroundColor: AppTheme.errorColor,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<AppointmentNotifier>(context);
    final appt = notifier.selectedAppointmentDetail;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Chi Tiết Lịch Hẹn'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && appt == null
          ? const LoadingWidget(message: 'Đang tải chi tiết lịch hẹn...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: _loadDetail)
              : appt == null
                  ? const Center(child: Text('Lịch hẹn không tồn tại.'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Card trạng thái khám
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                children: [
                                  CircleAvatar(
                                    radius: 36,
                                    backgroundColor: _getStatusColor(appt['status'] ?? 'pending').withOpacity(0.1),
                                    child: Icon(
                                      Icons.event_available_rounded,
                                      color: _getStatusColor(appt['status'] ?? 'pending'),
                                      size: 40,
                                    ),
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    _getStatusText(appt['status'] ?? 'pending').toUpperCase(),
                                    style: TextStyle(
                                      color: _getStatusColor(appt['status'] ?? 'pending'),
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      letterSpacing: 1,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  const Text(
                                    'Trạng thái lịch hẹn khám bệnh',
                                    style: TextStyle(color: Colors.black54, fontSize: 13),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Card thông tin chi tiết
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(20.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'Thông tin lượt khám',
                                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppTheme.primaryColor),
                                  ),
                                  const Divider(height: 24),
                                  
                                  _buildDetailRow(Icons.person_rounded, 'Bác sĩ phụ trách:', appt['doctor_name'] ?? 'Bác sĩ'),
                                  _buildDetailRow(Icons.health_and_safety_rounded, 'Khoa lâm sàng:', appt['department_name'] ?? 'Khoa'),
                                  
                                  // Xử lý hiển thị Phòng khám an toàn
                                  _buildDetailRow(
                                    Icons.door_sliding_rounded,
                                    'Phòng khám:',
                                    appt['room_name'] ?? 'Đang sắp xếp phòng khám...',
                                    valueColor: appt['room_name'] != null ? Colors.black87 : Colors.grey,
                                  ),

                                  _buildDetailRow(
                                    Icons.access_time_filled_rounded,
                                    'Thời gian khám:',
                                    _formatDateTime(appt['appointment_date'] ?? ''),
                                  ),

                                  _buildDetailRow(Icons.description_rounded, 'Lý do khám:', appt['reason'] ?? '', isMultiLine: true),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 24),

                          // Hiển thị nút Hủy lịch nếu điều kiện cho phép
                          if ((appt['status'] == 'pending' || appt['status'] == 'confirmed') &&
                              _isFutureTime(appt['appointment_date'] ?? ''))
                            notifier.isLoading
                                ? const Center(child: CircularProgressIndicator())
                                : OutlinedButton.icon(
                                    style: OutlinedButton.styleFrom(
                                      foregroundColor: AppTheme.errorColor,
                                      side: const BorderSide(color: AppTheme.errorColor, width: 1.5),
                                    ),
                                    onPressed: _cancelAppt,
                                    icon: const Icon(Icons.cancel_schedule_send_rounded, size: 20),
                                    label: const Text('HỦY LỊCH HẸN KHÁM'),
                                  ),
                        ],
                      ),
                    ),
    );
  }

  Widget _buildDetailRow(IconData icon, String label, String value, {bool isMultiLine = false, Color valueColor = Colors.black87}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        crossAxisAlignment: isMultiLine ? CrossAxisAlignment.start : CrossAxisAlignment.center,
        children: [
          Icon(icon, color: Colors.grey.shade600, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: isMultiLine
                ? Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(label, style: const TextStyle(fontSize: 12, color: Colors.black54)),
                      const SizedBox(height: 4),
                      Text(value, style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: valueColor)),
                    ],
                  )
                : Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(label, style: const TextStyle(fontSize: 14, color: Colors.black54)),
                      Flexible(
                        child: Text(
                          value,
                          textAlign: TextAlign.right,
                          style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: valueColor),
                        ),
                      ),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  String _formatDateTime(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('HH:mm, dd/MM/yyyy').format(parsed);
  }

  bool _isFutureTime(String dateStr) {
    if (dateStr.isEmpty) return false;
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return false;
    return parsed.isAfter(DateTime.now());
  }
}
