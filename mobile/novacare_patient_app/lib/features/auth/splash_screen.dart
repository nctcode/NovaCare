import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/helpers/biometric_helper.dart';
import '../../core/storage/secure_storage_service.dart';
import 'auth_notifier.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  bool _showBiometricRetry = false;

  @override
  void initState() {
    super.initState();
    _initApp();
  }

  Future<void> _initApp() async {
    // Đợi 1.5 giây tạo hiệu ứng thương hiệu chuyên nghiệp
    await Future.delayed(const Duration(milliseconds: 1500));
    
    if (!mounted) return;

    final authNotifier = Provider.of<AuthNotifier>(context, listen: false);
    await authNotifier.checkAuthStatus();

    if (!mounted) return;

    if (authNotifier.isAuthenticated) {
      // Kiểm tra xem người dùng có kích hoạt xác thực sinh trắc học hay không
      final storage = SecureStorageService();
      final bioEnabled = await storage.isBiometricEnabled();
      
      if (bioEnabled) {
        final available = await BiometricHelper.isBiometricAvailable();
        if (available) {
          final success = await BiometricHelper.authenticate();
          if (success) {
            if (mounted) {
              Navigator.pushReplacementNamed(context, '/home');
            }
            return;
          } else {
            // Xác thực vân tay/Face ID bị thất bại hoặc bị hủy
            setState(() {
              _showBiometricRetry = true;
            });
            return;
          }
        }
      }
      
      // Không bật sinh trắc học hoặc thiết bị không hỗ trợ -> Vào thẳng Home
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  void _retryBiometric() async {
    final success = await BiometricHelper.authenticate();
    if (success && mounted) {
      Navigator.pushReplacementNamed(context, '/home');
    }
  }

  void _bypassToLogin() async {
    // Đăng xuất và xóa sạch credentials cũ để đăng nhập lại
    final authNotifier = Provider.of<AuthNotifier>(context, listen: false);
    await authNotifier.logout();
    if (mounted) {
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.primaryColor,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            if (!_showBiometricRetry) ...[
              const Icon(
                Icons.local_hospital_rounded,
                color: Colors.white,
                size: 96,
              ),
              const SizedBox(height: 24),
              const Text(
                'NovaCare',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 32,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1.5,
                ),
              ),
              const SizedBox(height: 8),
              const Text(
                'Hệ Thống Y Tế Thông Minh',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 16,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 48),
              const CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
              ),
            ] else ...[
              const Icon(
                Icons.fingerprint_rounded,
                color: Colors.white,
                size: 80,
              ),
              const SizedBox(height: 24),
              const Text(
                'Yêu Cầu Xác Thực',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 32.0),
                child: Text(
                  'Vui lòng xác thực sinh trắc học (vân tay hoặc Face ID) để mở khóa nhanh NovaCare.',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 14,
                    height: 1.4,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
              const SizedBox(height: 40),
              ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: AppTheme.primaryColor,
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
                ),
                icon: const Icon(Icons.security_rounded),
                label: const Text('XÁC THỰC LẠI', style: TextStyle(fontWeight: FontWeight.bold)),
                onPressed: _retryBiometric,
              ),
              const SizedBox(height: 16),
              TextButton(
                style: TextButton.styleFrom(foregroundColor: Colors.white70),
                onPressed: _bypassToLogin,
                child: const Text('Đăng nhập bằng tài khoản khác', style: TextStyle(decoration: TextDecoration.underline)),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
