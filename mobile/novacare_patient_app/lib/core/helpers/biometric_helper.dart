import 'package:flutter/material.dart';
import 'package:local_auth/local_auth.dart';

class BiometricHelper {
  static final LocalAuthentication _auth = LocalAuthentication();

  /// Kiểm tra thiết bị có hỗ trợ xác thực sinh trắc học hay không
  static Future<bool> isBiometricAvailable() async {
    try {
      final bool canAuthenticateWithBiometrics = await _auth.canCheckBiometrics;
      final bool canAuthenticate = canAuthenticateWithBiometrics || await _auth.isDeviceSupported();
      return canAuthenticate;
    } catch (e) {
      debugPrint("Lỗi kiểm tra tính khả dụng của sinh trắc học: $e");
      return false;
    }
  }

  /// Thực hiện xác thực Face ID / Vân tay
  static Future<bool> authenticate() async {
    try {
      if (!await isBiometricAvailable()) {
        return false;
      }
      return await _auth.authenticate(
        localizedReason: 'Vui lòng xác thực sinh trắc học để truy cập nhanh ứng dụng NovaCare',
        options: const AuthenticationOptions(
          stickyAuth: true,
          biometricOnly: false, // Cho phép dùng mã PIN/Pattern của máy làm dự phòng
        ),
      );
    } catch (e) {
      debugPrint("Lỗi xác thực sinh trắc học: $e");
      return false;
    }
  }
}
