import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../../core/theme/app_theme.dart';

class VNPayWebViewScreen extends StatefulWidget {
  final String paymentUrl;
  final int invoiceId;

  const VNPayWebViewScreen({
    super.key,
    required this.paymentUrl,
    required this.invoiceId,
  });

  @override
  State<VNPayWebViewScreen> createState() => _VNPayWebViewScreenState();
}

class _VNPayWebViewScreenState extends State<VNPayWebViewScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(Colors.white)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            setState(() {
              _isLoading = true;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
          },
          onNavigationRequest: (NavigationRequest request) {
            final String url = request.url;
            
            // Lắng nghe xem URL chuyển hướng có khớp với Return URL của Backend không
            if (url.contains('/payments/vnpay/return')) {
              // Giao dịch hoàn tất, đóng WebView và báo kết quả thành công cho màn hình trước
              Future.delayed(const Duration(seconds: 2), () {
                if (mounted) {
                  Navigator.pop(context, true);
                }
              });
              return NavigationDecision.prevent; // Ngăn chặn chuyển hướng tiếp theo
            }
            
            return NavigationDecision.navigate;
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.paymentUrl));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Thanh Toán VNPay'),
        leading: IconButton(
          icon: const Icon(Icons.close_rounded),
          onPressed: () {
            // Người dùng chủ động đóng WebView -> Giao dịch bị hủy
            Navigator.pop(context, false);
          },
        ),
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  CircularProgressIndicator(
                    valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor),
                  ),
                  SizedBox(height: 16),
                  Text(
                    'Đang kết nối cổng thanh toán...',
                    style: TextStyle(color: Colors.black54, fontSize: 14, fontWeight: FontWeight.w500),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
