import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/storage/secure_storage_service.dart';
import '../../core/helpers/biometric_helper.dart';
import '../../core/constants/api_constants.dart';
import '../auth/auth_notifier.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  final _storage = SecureStorageService();
  bool _useBiometric = false;
  String _currentBaseUrl = ApiConstants.baseUrl;
  bool _isBioHardwareAvailable = false;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  Future<void> _loadSettings() async {
    final bioEnabled = await _storage.isBiometricEnabled();
    final customUrl = await _storage.getCustomBaseUrl();
    final hardwareAvailable = await BiometricHelper.isBiometricAvailable();

    setState(() {
      _useBiometric = bioEnabled;
      if (customUrl != null && customUrl.isNotEmpty) {
        _currentBaseUrl = customUrl;
      }
      _isBioHardwareAvailable = hardwareAvailable;
    });
  }

  void _toggleBiometric(bool value) async {
    if (value) {
      // Yêu cầu xác thực thử trước khi cho phép kích hoạt bật
      final available = await BiometricHelper.isBiometricAvailable();
      if (!available) {
        _showErrorSnackBar('Thiết bị của bạn không hỗ trợ sinh trắc học.');
        return;
      }
      final success = await BiometricHelper.authenticate();
      if (!success) {
        _showErrorSnackBar('Xác thực thất bại. Không thể kích hoạt đăng nhập sinh trắc học.');
        return;
      }
    }

    await _storage.saveBiometricEnabled(value);
    setState(() {
      _useBiometric = value;
    });
    _showSuccessSnackBar(
      value 
          ? 'Đã kích hoạt đăng nhập bằng sinh trắc học.' 
          : 'Đã tắt đăng nhập bằng sinh trắc học.'
    );
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: AppTheme.errorColor),
    );
  }

  void _showSuccessSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.green),
    );
  }

  void _editBaseUrl() async {
    final controller = TextEditingController(text: _currentBaseUrl);
    final newUrl = await showDialog<String>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.dns_rounded, color: AppTheme.primaryColor),
              SizedBox(width: 8),
              Text('Cấu hình Base URL', style: TextStyle(fontSize: 18)),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text(
                'Nhập địa chỉ máy chủ API (Dành cho lập trình viên/Developer Mode):',
                style: TextStyle(fontSize: 12, color: Colors.black54),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: controller,
                decoration: const InputDecoration(
                  labelText: 'API Base URL',
                  hintText: 'http://10.0.2.2/NovaCare/api/v1',
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('HỦY BỎ'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context, controller.text.trim());
              },
              child: const Text('LƯU LẠI'),
            ),
          ],
        );
      },
    );

    if (newUrl != null) {
      await _storage.saveCustomBaseUrl(newUrl);
      setState(() {
        _currentBaseUrl = newUrl.isEmpty ? ApiConstants.baseUrl : newUrl;
      });
      _showSuccessSnackBar('Đã cập nhật Base URL. Vui lòng khởi động lại app nếu cần.');
    }
  }

  void _showPrivacyPolicy() {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Chính Sách Quyền Riêng Tư'),
          content: const SingleChildScrollView(
            child: Text(
              'Hệ thống y tế NovaCare cam kết bảo mật tuyệt đối các thông tin y khoa, bệnh án, và hồ sơ lịch khám của người bệnh.\n\n'
              'Chúng tôi không chia sẻ dữ liệu sức khỏe của bạn cho bên thứ ba ngoại trừ các cơ quan y tế có thẩm quyền dưới sự đồng ý của bệnh nhân.\n\n'
              'Dữ liệu đăng nhập sinh trắc học của bạn được lưu và xử lý an toàn trực tiếp trên phần cứng thiết bị của bạn thông qua hệ thống bảo mật cục bộ của Android/iOS, NovaCare tuyệt đối không tải dữ liệu sinh trắc học của bạn lên máy chủ.',
              style: TextStyle(fontSize: 13, height: 1.4),
            ),
          ),
          actions: [
            ElevatedButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('ĐỒNG Ý'),
            ),
          ],
        );
      },
    );
  }

  void _handleLogout(BuildContext context, AuthNotifier notifier) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Xác nhận đăng xuất'),
          content: const Text('Bạn có chắc chắn muốn đăng xuất tài khoản bệnh nhân khỏi thiết bị này không?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('HỦY BỎ'),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.errorColor),
              onPressed: () => Navigator.pop(context, true),
              child: const Text('ĐĂNG XUẤT'),
            ),
          ],
        );
      },
    );

    if (confirmed == true) {
      await notifier.logout();
      if (context.mounted) {
        Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final authNotifier = Provider.of<AuthNotifier>(context);
    final name = authNotifier.userInfo['name'] ?? 'Bệnh nhân';
    final email = authNotifier.userInfo['email'] ?? 'vi_du@gmail.com';

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Cấu Hình Ứng Dụng'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(vertical: 16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Thông tin cá nhân Header Card
            Container(
              padding: const EdgeInsets.all(20.0),
              color: Colors.white,
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 30,
                    backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
                    child: const Icon(Icons.person_rounded, color: AppTheme.primaryColor, size: 36),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          name,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.black87),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          email,
                          style: const TextStyle(color: Colors.black54, fontSize: 13),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Nhóm tính năng chính
            _buildSectionHeader('Tính năng của tôi'),
            _buildSettingTile(context, Icons.person_outline_rounded, 'Hồ sơ sức khỏe cá nhân', '/profile'),
            _buildSettingTile(context, Icons.calendar_month_rounded, 'Quản lý lịch khám bệnh', '/my-appointments'),
            _buildSettingTile(context, Icons.assignment_outlined, 'Lịch sử bệnh án y khoa', '/records'),
            _buildSettingTile(context, Icons.receipt_long_rounded, 'Hóa đơn viện phí & thanh toán', '/invoices'),
            _buildSettingTile(context, Icons.notifications_none_rounded, 'Thông báo từ hệ thống', '/notifications'),
            _buildSettingTile(context, Icons.support_agent_rounded, 'Trò chuyện y khoa AI', '/ai-chat'),
            
            const SizedBox(height: 16),

            // Nhóm bảo mật sinh trắc học
            _buildSectionHeader('Bảo mật sinh trắc học'),
            Container(
              color: Colors.white,
              child: SwitchListTile(
                activeColor: AppTheme.primaryColor,
                secondary: const Icon(Icons.fingerprint_rounded, color: AppTheme.primaryColor),
                title: const Text('Đăng nhập bằng Vân tay / Face ID', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
                subtitle: Text(
                  _isBioHardwareAvailable 
                      ? 'Kích hoạt mở khóa nhanh ứng dụng không cần nhập mật khẩu' 
                      : 'Thiết bị không hỗ trợ hoặc chưa đăng ký sinh trắc học',
                  style: const TextStyle(fontSize: 11),
                ),
                value: _useBiometric,
                onChanged: _isBioHardwareAvailable ? _toggleBiometric : null,
              ),
            ),

            const SizedBox(height: 16),

            // Nhóm cấu hình máy chủ (Developer mode)
            _buildSectionHeader('Cấu hình máy chủ (Developer Mode)'),
            Container(
              color: Colors.white,
              child: ListTile(
                leading: const Icon(Icons.dns_rounded, color: AppTheme.primaryColor),
                title: const Text('Địa chỉ API Base URL', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
                subtitle: Text(_currentBaseUrl, style: const TextStyle(fontSize: 11)),
                trailing: const Icon(Icons.edit_rounded, size: 20, color: Colors.black38),
                onTap: _editBaseUrl,
              ),
            ),

            const SizedBox(height: 16),

            // Nhóm chính sách & thông tin phiên bản
            _buildSectionHeader('Thông tin pháp lý'),
            Container(
              color: Colors.white,
              child: ListTile(
                leading: const Icon(Icons.privacy_tip_outlined, color: AppTheme.primaryColor),
                title: const Text('Điều khoản & Quyền riêng tư', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
                trailing: const Icon(Icons.chevron_right_rounded, color: Colors.black38),
                onTap: _showPrivacyPolicy,
              ),
            ),

            const SizedBox(height: 24),

            // Nút đăng xuất (Logout)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.errorColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                ),
                icon: const Icon(Icons.logout_rounded),
                label: const Text('ĐĂNG XUẤT TÀI KHOẢN'),
                onPressed: () => _handleLogout(context, authNotifier),
              ),
            ),

            const SizedBox(height: 32),
            const Center(
              child: Text(
                'NovaCare Patient Mobile App\nPhiên bản v1.1.0',
                style: TextStyle(color: Colors.black38, fontSize: 11, height: 1.4),
                textAlign: TextAlign.center,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 16, bottom: 8, top: 8),
      child: Text(
        title.toUpperCase(),
        style: const TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.bold,
          color: Colors.black54,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _buildSettingTile(BuildContext context, IconData icon, String title, String route) {
    return Container(
      color: Colors.white,
      child: Column(
        children: [
          ListTile(
            leading: Icon(icon, color: AppTheme.primaryColor),
            title: Text(title, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
            trailing: const Icon(Icons.chevron_right_rounded, color: Colors.black38),
            onTap: () {
              Navigator.pushNamed(context, route);
            },
          ),
          Divider(height: 1, indent: 56, color: Colors.grey.shade100),
        ],
      ),
    );
  }
}
