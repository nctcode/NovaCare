import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../auth/auth_notifier.dart';
import 'home_notifier.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    _refreshData();
  }

  Future<void> _refreshData() async {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<HomeNotifier>(context, listen: false).loadDashboardData();
    });
  }

  String _formatVND(dynamic amount) {
    if (amount == null) return '0 đ';
    final value = double.tryParse(amount.toString()) ?? 0.0;
    final formatter = NumberFormat.currency(locale: 'vi_VN', symbol: 'đ', decimalDigits: 0);
    return formatter.format(value).replaceAll('₫', 'đ');
  }

  @override
  Widget build(BuildContext context) {
    final homeNotifier = Provider.of<HomeNotifier>(context);
    final authNotifier = Provider.of<AuthNotifier>(context);
    final userName = authNotifier.userInfo['name'] ?? 'Bệnh nhân';

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text(
          'NovaCare',
          style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 0.5),
        ),
        actions: [
          // Nút thông báo kèm Badge số lượng chưa đọc
          Stack(
            alignment: Alignment.center,
            children: [
              IconButton(
                icon: const Icon(Icons.notifications_none_rounded, size: 26),
                onPressed: () {
                  Navigator.pushNamed(context, '/notifications').then((_) => _refreshData());
                },
              ),
              if (homeNotifier.unreadNotificationsCount > 0)
                Positioned(
                  right: 8,
                  top: 8,
                  child: Container(
                    padding: const EdgeInsets.all(2),
                    decoration: BoxDecoration(
                      color: AppTheme.errorColor,
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 1.5),
                    ),
                    constraints: const BoxConstraints(
                      minWidth: 16,
                      minHeight: 16,
                    ),
                    child: Text(
                      '${homeNotifier.unreadNotificationsCount}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.bold,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                ),
            ],
          ),
          IconButton(
            icon: const Icon(Icons.settings_outlined, size: 24),
            onPressed: () {
              Navigator.pushNamed(context, '/settings').then((_) => _refreshData());
            },
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: RefreshIndicator(
        color: AppTheme.primaryColor,
        onRefresh: _refreshData,
        child: homeNotifier.isLoading && homeNotifier.userMe == null
            ? const LoadingWidget(message: 'Đang tải thông tin dashboard...')
            : homeNotifier.errorMessage != null
                ? ErrorView(
                    message: homeNotifier.errorMessage!,
                    onRetry: _refreshData,
                  )
                : SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        // 1. Lời chào bệnh nhân
                        Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Row(
                            children: [
                              CircleAvatar(
                                radius: 24,
                                backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
                                child: Text(
                                  userName.isNotEmpty ? userName[0].toUpperCase() : 'B',
                                  style: const TextStyle(
                                    color: AppTheme.primaryColor,
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      'Xin chào,',
                                      style: TextStyle(
                                        color: Colors.grey.shade600,
                                        fontSize: 13,
                                      ),
                                    ),
                                    Text(
                                      userName,
                                      style: const TextStyle(
                                        color: Colors.black87,
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),

                        // 2. Banner trợ lý ảo AI
                        _buildAIAssistantBanner(context),

                        // 3. Section: Dịch vụ nhanh (Menu Grid)
                        _buildSectionHeader('Dịch vụ nhanh'),
                        _buildMenuGrid(context),

                        // 4. Section: Lịch hẹn hôm nay hoặc Lịch hẹn sắp tới
                        _buildSectionHeader('Lịch hẹn sắp tới'),
                        _buildUpcomingAppointmentsSection(context, homeNotifier),

                        // 5. Section: Hóa đơn cần thanh toán
                        _buildSectionHeader('Hóa đơn cần thanh toán'),
                        _buildPendingInvoicesSection(context, homeNotifier),

                        // 6. Section: Thông báo mới
                        _buildSectionHeader('Thông báo mới'),
                        _buildRecentNotificationsSection(context, homeNotifier),
                        
                        const SizedBox(height: 32),
                      ],
                    ),
                  ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 24, 16, 8),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
          color: Colors.black87,
        ),
      ),
    );
  }

  // Widget Banner trợ lý ảo AI
  Widget _buildAIAssistantBanner(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppTheme.primaryColor, Color(0xff1565c0)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primaryColor.withOpacity(0.2),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(16),
          onTap: () {
            Navigator.pushNamed(context, '/ai-chat').then((_) => _refreshData());
          },
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Trợ Lý Y Khoa AI 🤖',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 6),
                      Text(
                        'Chat tư vấn triệu chứng bệnh và đề xuất khoa phòng khám chuyên môn ngay lập tức.',
                        style: TextStyle(
                          color: Colors.white.withOpacity(0.87),
                          fontSize: 12,
                          height: 1.3,
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(width: 12),
                CircleAvatar(
                  radius: 24,
                  backgroundColor: Colors.white,
                  child: Icon(
                    Icons.chat_bubble_outline_rounded,
                    color: AppTheme.primaryColor,
                    size: 26,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Widget Grid Menu các chức năng (Dịch vụ nhanh)
  Widget _buildMenuGrid(BuildContext context) {
    final menuItems = [
      _MenuItem(Icons.event_note_rounded, 'Đặt Lịch Hẹn', '/departments', const Color(0xffe3f2fd), AppTheme.primaryColor),
      _MenuItem(Icons.calendar_month_rounded, 'Lịch Hẹn', '/my-appointments', const Color(0xffefebe9), const Color(0xff8d6e63)),
      _MenuItem(Icons.query_stats_rounded, 'Theo Dõi Lượt', '/queue', const Color(0xffe8f5e9), const Color(0xff4caf50)),
      _MenuItem(Icons.assignment_ind_rounded, 'Bệnh Án', '/records', const Color(0xfff3e5f5), const Color(0xff9c27b0)),
      _MenuItem(Icons.medication_rounded, 'Đơn Thuốc', '/prescriptions', const Color(0xfffff8e1), const Color(0xffffb300)),
      _MenuItem(Icons.receipt_long_rounded, 'Hóa Đơn', '/invoices', const Color(0xffe0f7fa), const Color(0xff00acc1)),
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 16.0),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3,
        crossAxisSpacing: 10,
        mainAxisSpacing: 10,
        childAspectRatio: 1.0,
      ),
      itemCount: menuItems.length,
      itemBuilder: (context, index) {
        final item = menuItems[index];
        return Card(
          margin: EdgeInsets.zero,
          elevation: 0.5,
          color: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: BorderSide(color: Colors.grey.shade100),
          ),
          child: InkWell(
            borderRadius: BorderRadius.circular(12),
            onTap: () {
              Navigator.pushNamed(context, item.route).then((_) => _refreshData());
            },
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                CircleAvatar(
                  backgroundColor: item.bgColor,
                  radius: 20,
                  child: Icon(item.icon, color: item.iconColor, size: 20),
                ),
                const SizedBox(height: 6),
                Text(
                  item.title,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  // Widget Lịch hẹn sắp tới
  Widget _buildUpcomingAppointmentsSection(BuildContext context, HomeNotifier homeNotifier) {
    if (homeNotifier.upcomingAppointments.isEmpty) {
      return Container(
        margin: const EdgeInsets.symmetric(horizontal: 16.0),
        padding: const EdgeInsets.all(20.0),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: Column(
          children: [
            Icon(Icons.calendar_today_rounded, size: 36, color: Colors.grey.shade400),
            const SizedBox(height: 10),
            const Text(
              'Chưa có lịch hẹn khám nào sắp tới.',
              style: TextStyle(color: Colors.black54, fontSize: 13, fontWeight: FontWeight.w500),
            ),
            const SizedBox(height: 12),
            ElevatedButton.icon(
              icon: const Icon(Icons.add_rounded, size: 18),
              label: const Text('ĐẶT LỊCH NGAY'),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                textStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
              ),
              onPressed: () {
                Navigator.pushNamed(context, '/departments').then((_) => _refreshData());
              },
            ),
          ],
        ),
      );
    }

    final appt = homeNotifier.upcomingAppointments.first;
    final doctor = appt['doctor_name'] ?? 'Bác sĩ';
    final dept = appt['department_name'] ?? 'Khoa phòng';
    final date = appt['appointment_date'] ?? '';
    final time = appt['appointment_time'] ?? '';
    final status = appt['status'] ?? 'pending';

    Color badgeColor = Colors.orange;
    String statusText = 'Chờ xác nhận';
    if (status == 'confirmed') {
      badgeColor = AppTheme.primaryColor;
      statusText = 'Đã xác nhận';
    }

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16.0),
      elevation: 0.5,
      color: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: Colors.grey.shade200),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () {
          Navigator.pushNamed(context, '/my-appointments').then((_) => _refreshData());
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
                        '$time - $date',
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.primaryColor),
                      ),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: badgeColor.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      statusText,
                      style: TextStyle(color: badgeColor, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
              const Divider(height: 20),
              Text(
                doctor,
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: Colors.black87),
              ),
              const SizedBox(height: 4),
              Text(
                'Khoa: $dept',
                style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
              ),
              if (appt['reason'] != null && appt['reason'].toString().trim().isNotEmpty) ...[
                const SizedBox(height: 6),
                Text(
                  'Lý do: "${appt['reason']}"',
                  style: TextStyle(color: Colors.grey.shade500, fontSize: 12, fontStyle: FontStyle.italic),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  // Widget Hóa đơn cần thanh toán
  Widget _buildPendingInvoicesSection(BuildContext context, HomeNotifier homeNotifier) {
    if (homeNotifier.pendingInvoices.isEmpty) {
      return Container(
        margin: const EdgeInsets.symmetric(horizontal: 16.0),
        padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: const Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.check_circle_outline_rounded, color: Colors.green, size: 24),
            SizedBox(width: 8),
            Text(
              'Không có hóa đơn nào cần thanh toán.',
              style: TextStyle(color: Colors.green, fontSize: 13, fontWeight: FontWeight.w600),
            ),
          ],
        ),
      );
    }

    return Column(
      children: homeNotifier.pendingInvoices.take(2).map((inv) {
        final code = inv['invoice_code'] ?? 'INV';
        final amount = inv['amount'] ?? 0;
        final date = inv['created_at'] ?? '';
        
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 4.0),
          elevation: 0.5,
          color: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: BorderSide(color: Colors.grey.shade200),
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            title: Text(
              code,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
            ),
            subtitle: Text(
              'Ngày tạo: $date',
              style: const TextStyle(fontSize: 11, color: Colors.black54),
            ),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  _formatVND(amount),
                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: AppTheme.errorColor),
                ),
                const SizedBox(height: 4),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                  decoration: BoxDecoration(
                    color: AppTheme.errorColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: const Text(
                    'Thanh toán ngay',
                    style: TextStyle(color: AppTheme.errorColor, fontSize: 9, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
            onTap: () {
              Navigator.pushNamed(context, '/invoices').then((_) => _refreshData());
            },
          ),
        );
      }).toList(),
    );
  }

  // Widget Thông báo mới
  Widget _buildRecentNotificationsSection(BuildContext context, HomeNotifier homeNotifier) {
    if (homeNotifier.recentNotifications.isEmpty) {
      return Container(
        margin: const EdgeInsets.symmetric(horizontal: 16.0),
        padding: const EdgeInsets.all(20.0),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: Center(
          child: Text(
            'Chưa có thông báo mới nào.',
            style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
          ),
        ),
      );
    }

    return Column(
      children: homeNotifier.recentNotifications.map((notif) {
        final title = notif['title'] ?? 'Thông báo';
        final body = notif['body'] ?? '';
        final date = notif['created_at'] ?? '';
        final isUnread = notif['read_at'] == null;

        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 4.0),
          elevation: 0.5,
          color: isUnread ? const Color(0xffe3f2fd).withOpacity(0.3) : Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: BorderSide(color: Colors.grey.shade200),
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
            leading: CircleAvatar(
              backgroundColor: isUnread ? const Color(0xffe3f2fd) : Colors.grey.shade100,
              radius: 18,
              child: Icon(
                _getNotificationIcon(notif['type']),
                color: isUnread ? AppTheme.primaryColor : Colors.grey.shade600,
                size: 18,
              ),
            ),
            title: Row(
              children: [
                Expanded(
                  child: Text(
                    title,
                    style: TextStyle(
                      fontWeight: isUnread ? FontWeight.bold : FontWeight.normal,
                      fontSize: 13,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                if (isUnread)
                  Container(
                    width: 8,
                    height: 8,
                    decoration: const BoxDecoration(color: AppTheme.primaryColor, shape: BoxShape.circle),
                  ),
              ],
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 2),
                Text(
                  body,
                  style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  date,
                  style: TextStyle(fontSize: 10, color: Colors.grey.shade400),
                ),
              ],
            ),
            onTap: () {
              Navigator.pushNamed(context, '/notifications').then((_) => _refreshData());
            },
          ),
        );
      }).toList(),
    );
  }

  IconData _getNotificationIcon(dynamic type) {
    final typeStr = type.toString().toLowerCase();
    if (typeStr == 'appointment') {
      return Icons.calendar_month_rounded;
    } else if (typeStr == 'payment') {
      return Icons.payment_rounded;
    } else if (typeStr == 'ai_alert') {
      return Icons.warning_rounded;
    }
    return Icons.notifications_none_rounded;
  }
}

class _MenuItem {
  final IconData icon;
  final String title;
  final String route;
  final Color bgColor;
  final Color iconColor;

  _MenuItem(this.icon, this.title, this.route, this.bgColor, this.iconColor);
}
