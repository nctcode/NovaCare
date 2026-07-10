import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../payments/vnpay_webview_screen.dart';
import 'invoice_notifier.dart';

class InvoiceDetailScreen extends StatefulWidget {
  final int invoiceId;

  const InvoiceDetailScreen({super.key, required this.invoiceId});

  @override
  State<InvoiceDetailScreen> createState() => _InvoiceDetailScreenState();
}

class _InvoiceDetailScreenState extends State<InvoiceDetailScreen> {
  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  void _loadDetail() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<InvoiceNotifier>(context, listen: false).loadInvoiceDetail(widget.invoiceId);
    });
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
        return AppTheme.errorColor;
      case 'paid':
        return Colors.green;
      case 'cancelled':
        return Colors.grey;
      default:
        return Colors.black54;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'pending':
        return 'Chờ thanh toán';
      case 'paid':
        return 'Đã thanh toán';
      case 'cancelled':
        return 'Đã hủy';
      default:
        return status;
    }
  }

  String _formatCurrency(dynamic amount) {
    final double val = double.tryParse(amount.toString()) ?? 0.0;
    final formatter = NumberFormat.currency(locale: 'vi_VN', symbol: 'đ');
    return formatter.format(val);
  }

  String _formatDateTime(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('HH:mm, dd/MM/yyyy').format(parsed);
  }

  // Khởi động cổng thanh toán VNPay
  void _startPayment(BuildContext context) async {
    final notifier = Provider.of<InvoiceNotifier>(context, listen: false);
    final paymentUrl = await notifier.createVNPayPayment(widget.invoiceId);

    if (paymentUrl != null && mounted) {
      // Mở màn hình WebView VNPay
      final paymentResult = await Navigator.push<bool>(
        context,
        MaterialPageRoute(
          builder: (context) => VNPayWebViewScreen(
            paymentUrl: paymentUrl,
            invoiceId: widget.invoiceId,
          ),
        ),
      );

      if (!mounted) return;

      // Sau khi đóng WebView, gọi API check lại trạng thái cập nhật từ backend
      final paymentData = await notifier.checkPaymentStatus(widget.invoiceId);

      if (!mounted) return;

      if (paymentData != null && paymentData['payment_status'] == 'paid') {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Thanh toán viện phí thành công!'),
            backgroundColor: Colors.green,
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              paymentResult == true
                  ? 'Đang kiểm tra kết quả thanh toán...'
                  : 'Giao dịch thanh toán đã bị hủy hoặc chưa hoàn tất.',
            ),
            backgroundColor: paymentResult == true ? Colors.orange : AppTheme.errorColor,
          ),
        );
      }
      _loadDetail(); // Tải lại để cập nhật UI
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(notifier.errorMessage ?? 'Không thể khởi tạo thanh toán.'),
            backgroundColor: AppTheme.errorColor,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<InvoiceNotifier>(context);
    final inv = notifier.selectedInvoiceDetail;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Chi Tiết Hóa Đơn'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && inv == null
          ? const LoadingWidget(message: 'Đang tải chi tiết hóa đơn...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: _loadDetail)
              : inv == null
                  ? const Center(child: Text('Hóa đơn không tồn tại.'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Card trạng thái & mã hóa đơn
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                children: [
                                  Text(
                                    inv['invoice_code'] ?? 'INV-...',
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black87,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: _getStatusColor(inv['payment_status'] ?? 'pending').withOpacity(0.1),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(
                                      _getStatusText(inv['payment_status'] ?? 'pending').toUpperCase(),
                                      style: TextStyle(
                                        color: _getStatusColor(inv['payment_status'] ?? 'pending'),
                                        fontWeight: FontWeight.bold,
                                        fontSize: 13,
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Thời gian tạo: ${_formatDateTime(inv['created_at'] ?? '')}',
                                    style: const TextStyle(color: Colors.black54, fontSize: 13),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Card Bảng chi tiết dòng tiền
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
                                    'Chi tiết thanh toán viện phí',
                                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppTheme.primaryColor),
                                  ),
                                  const Divider(height: 24),
                                  
                                  _buildInvoiceRow('Tổng tiền dịch vụ:', _formatCurrency(inv['total_amount'] ?? 0.0)),
                                  _buildInvoiceRow('Miễn giảm ưu đãi:', '- ${_formatCurrency(inv['discount'] ?? 0.0)}', color: Colors.green),
                                  _buildInvoiceRow('Bảo hiểm BHYT chi trả:', '- ${_formatCurrency(inv['insurance_coverage'] ?? 0.0)}', color: Colors.green),
                                  
                                  const Divider(height: 24),
                                  _buildInvoiceRow(
                                    'Tổng tiền cần thanh toán:',
                                    _formatCurrency(inv['final_amount'] ?? 0.0),
                                    isTotal: true,
                                  ),
                                  
                                  if (inv['payment_method'] != null) ...[
                                    const Divider(height: 24),
                                    _buildInvoiceRow(
                                      'Phương thức thanh toán:',
                                      inv['payment_method'].toString().toUpperCase(),
                                    ),
                                  ],
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 24),

                          // Nút thanh toán nếu trạng thái là pending
                          if (inv['payment_status'] == 'pending')
                            notifier.isLoading
                                ? const Center(child: CircularProgressIndicator())
                                : ElevatedButton.icon(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: AppTheme.errorColor,
                                      foregroundColor: Colors.white,
                                    ),
                                    onPressed: () => _startPayment(context),
                                    icon: const Icon(Icons.payment_rounded),
                                    label: const Text('THANH TOÁN VNPAY (SANDBOX)'),
                                  ),
                        ],
                      ),
                    ),
    );
  }

  Widget _buildInvoiceRow(String label, String value, {bool isTotal = false, Color color = Colors.black87}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: isTotal ? 15 : 13,
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              color: isTotal ? Colors.black87 : Colors.black54,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: isTotal ? 20 : 14,
              fontWeight: FontWeight.bold,
              color: isTotal ? AppTheme.errorColor : color,
            ),
          ),
        ],
      ),
    );
  }
}
