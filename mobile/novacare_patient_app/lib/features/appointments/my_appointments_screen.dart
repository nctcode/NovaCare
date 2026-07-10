import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'appointment_notifier.dart';

class MyAppointmentsScreen extends StatefulWidget {
  const MyAppointmentsScreen({super.key});

  @override
  State<MyAppointmentsScreen> createState() => _MyAppointmentsScreenState();
}

class _MyAppointmentsScreenState extends State<MyAppointmentsScreen> {
  String _selectedStatus = 'all';
  int _currentPage = 1;

  final Map<String, String> _statuses = {
    'all': 'Tất cả',
    'pending': 'Chờ xác nhận',
    'confirmed': 'Đã xác nhận',
    'completed': 'Đã khám',
    'cancelled': 'Đã hủy',
  };

  @override
  void initState() {
    super.initState();
    _loadAppointments();
  }

  void _loadAppointments({int page = 1}) {
    _currentPage = page;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppointmentNotifier>(context, listen: false).loadMyAppointments(
        status: _selectedStatus,
        page: _currentPage,
      );
    });
  }

  Future<void> _handleRefresh() async {
    _loadAppointments(page: 1);
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
        return Colors.orange; // pending: vàng/cam
      case 'confirmed':
        return Colors.blue; // confirmed: xanh dương
      case 'completed':
        return Colors.green; // completed: xanh lá
      case 'cancelled':
        return Colors.grey; // cancelled: xám/đỏ nhạt
      default:
        return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    return _statuses[status] ?? status;
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<AppointmentNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Lịch Hẹn Của Tôi'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Filter Status Horizontal Row (Bộ lọc chip trạng thái)
          Container(
            height: 56,
            color: Colors.white,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              children: _statuses.entries.map((entry) {
                final isSelected = _selectedStatus == entry.key;
                return Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4.0),
                  child: ChoiceChip(
                    label: Text(
                      entry.value,
                      style: TextStyle(
                        color: isSelected ? Colors.white : Colors.black87,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                    selected: isSelected,
                    selectedColor: AppTheme.primaryColor,
                    backgroundColor: Colors.grey.shade100,
                    showCheckmark: false,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                      side: BorderSide(
                        color: isSelected ? AppTheme.primaryColor : Colors.grey.shade200,
                      ),
                    ),
                    onSelected: (selected) {
                      if (selected) {
                        setState(() {
                          _selectedStatus = entry.key;
                        });
                        _loadAppointments(page: 1);
                      }
                    },
                  ),
                );
              }).toList(),
            ),
          ),
          const Divider(height: 1, thickness: 1),

          // Appointments List with Pull-to-refresh
          Expanded(
            child: RefreshIndicator(
              color: AppTheme.primaryColor,
              onRefresh: _handleRefresh,
              child: notifier.isLoading && notifier.myAppointments.isEmpty
                  ? const LoadingWidget(message: 'Đang tải danh sách lịch hẹn...')
                  : notifier.errorMessage != null
                      ? ErrorView(
                          message: notifier.errorMessage!,
                          onRetry: () => _loadAppointments(page: _currentPage),
                        )
                      : notifier.myAppointments.isEmpty
                          ? const SingleChildScrollView(
                              physics: AlwaysScrollableScrollPhysics(),
                              child: Padding(
                                padding: EdgeInsets.all(40.0),
                                child: EmptyState(
                                  message: 'Không tìm thấy lịch hẹn khám nào.',
                                  icon: Icons.calendar_today_rounded,
                                ),
                              ),
                            )
                          : ListView.builder(
                              physics: const AlwaysScrollableScrollPhysics(),
                              padding: const EdgeInsets.all(12),
                              itemCount: notifier.myAppointments.length,
                              itemBuilder: (context, index) {
                                final appt = notifier.myAppointments[index];
                                final id = appt['id'] as int;
                                final doctor = appt['doctor_name'] ?? 'Bác sĩ';
                                final dept = appt['department_name'] ?? 'Khoa phòng';
                                final date = appt['appointment_date'] ?? '';
                                var time = appt['appointment_time'] ?? '';
                                final reason = appt['reason'] ?? 'Không rõ lý do';
                                final status = appt['status'] ?? 'pending';

                                // Định dạng lại giờ hh:mm
                                if (time.length > 5) {
                                  time = time.substring(0, 5);
                                }
                                final formattedDateTime = time.isNotEmpty ? "$time - $date" : date;

                                return Card(
                                  margin: const EdgeInsets.symmetric(vertical: 6),
                                  elevation: 0.5,
                                  color: Colors.white,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    side: BorderSide(color: Colors.grey.shade100),
                                  ),
                                  child: InkWell(
                                    borderRadius: BorderRadius.circular(12),
                                    onTap: () {
                                      Navigator.pushNamed(
                                        context,
                                        '/appointment-detail',
                                        arguments: id,
                                      ).then((_) => _loadAppointments(page: _currentPage));
                                    },
                                    child: Padding(
                                      padding: const EdgeInsets.all(16.0),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Row(
                                                children: [
                                                  const Icon(Icons.alarm_rounded, color: AppTheme.primaryColor, size: 18),
                                                  const SizedBox(width: 6),
                                                  Text(
                                                    formattedDateTime,
                                                    style: const TextStyle(
                                                      fontWeight: FontWeight.bold,
                                                      color: AppTheme.primaryColor,
                                                      fontSize: 14,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              Container(
                                                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                                decoration: BoxDecoration(
                                                  color: _getStatusColor(status).withOpacity(0.1),
                                                  borderRadius: BorderRadius.circular(8),
                                                ),
                                                child: Text(
                                                  _getStatusText(status),
                                                  style: TextStyle(
                                                    color: _getStatusColor(status),
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const Divider(height: 20),
                                          Row(
                                            children: [
                                              CircleAvatar(
                                                backgroundColor: AppTheme.primaryColor.withOpacity(0.08),
                                                radius: 20,
                                                child: const Icon(Icons.person_outline_rounded, color: AppTheme.primaryColor),
                                              ),
                                              const SizedBox(width: 12),
                                              Expanded(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text(
                                                      doctor,
                                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                                    ),
                                                    Text(
                                                      dept,
                                                      style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 12),
                                          Text(
                                            'Lý do: "$reason"',
                                            style: TextStyle(fontSize: 12, color: Colors.grey.shade700, fontStyle: FontStyle.italic),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
            ),
          ),

          // Nút chuyển trang (Phân trang)
          if (notifier.totalPages > 1)
            Container(
              padding: const EdgeInsets.symmetric(vertical: 8),
              color: Colors.white,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  IconButton(
                    icon: const Icon(Icons.arrow_back_ios_rounded, size: 18),
                    onPressed: _currentPage > 1 ? () => _loadAppointments(page: _currentPage - 1) : null,
                  ),
                  Text('Trang $_currentPage / ${notifier.totalPages}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  IconButton(
                    icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
                    onPressed: _currentPage < notifier.totalPages ? () => _loadAppointments(page: _currentPage + 1) : null,
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
