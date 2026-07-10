import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'invoice_notifier.dart';

class InvoicesScreen extends StatefulWidget {
  const InvoicesScreen({super.key});

  @override
  State<InvoicesScreen> createState() => _InvoicesScreenState();
}

class _InvoicesScreenState extends State<InvoicesScreen> {
  String _selectedStatus = 'all';
  int _currentPage = 1;

  final Map<String, String> _statuses = {
    'all': 'Tất cả',
    'pending': 'Chưa thanh toán',
    'paid': 'Đã thanh toán',
    'cancelled': 'Đã hủy',
  };

  @override
  void initState() {
    super.initState();
    _loadInvoices();
  }

  void _loadInvoices({int page = 1}) {
    _currentPage = page;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<InvoiceNotifier>(context, listen: false).loadMyInvoices(
        status: _selectedStatus,
        page: _currentPage,
      );
    });
  }

  Future<void> _handleRefresh() async {
    _loadInvoices(page: 1);
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
    return _statuses[status] ?? status;
  }

  String _formatCurrency(dynamic amount) {
    final double val = double.tryParse(amount.toString()) ?? 0.0;
    final formatter = NumberFormat.currency(locale: 'vi_VN', symbol: 'đ', decimalDigits: 0);
    return formatter.format(val).replaceAll('₫', 'đ');
  }

  String _formatDateTime(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('HH:mm - dd/MM/yyyy').format(parsed);
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<InvoiceNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Hóa Đơn Viện Phí'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Filter Chips Horizontal Row (Lọc trạng thái hóa đơn)
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
                    selectedColor: isSelected && entry.key == 'pending' ? AppTheme.errorColor : AppTheme.primaryColor,
                    backgroundColor: Colors.grey.shade100,
                    showCheckmark: false,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                      side: BorderSide(
                        color: isSelected ? Colors.transparent : Colors.grey.shade200,
                      ),
                    ),
                    onSelected: (selected) {
                      if (selected) {
                        setState(() {
                          _selectedStatus = entry.key;
                        });
                        _loadInvoices(page: 1);
                      }
                    },
                  ),
                );
              }).toList(),
            ),
          ),
          const Divider(height: 1, thickness: 1),

          // Invoices List with Pull-to-refresh
          Expanded(
            child: RefreshIndicator(
              color: AppTheme.primaryColor,
              onRefresh: _handleRefresh,
              child: notifier.isLoading && notifier.invoices.isEmpty
                  ? const LoadingWidget(message: 'Đang tải danh sách hóa đơn...')
                  : notifier.errorMessage != null
                      ? ErrorView(
                          message: notifier.errorMessage!,
                          onRetry: () => _loadInvoices(page: _currentPage),
                        )
                      : notifier.invoices.isEmpty
                          ? const SingleChildScrollView(
                              physics: AlwaysScrollableScrollPhysics(),
                              child: Padding(
                                padding: EdgeInsets.all(40.0),
                                child: EmptyState(
                                  message: 'Không tìm thấy hóa đơn viện phí nào.',
                                  icon: Icons.receipt_long_rounded,
                                ),
                              ),
                            )
                          : ListView.builder(
                              physics: const AlwaysScrollableScrollPhysics(),
                              padding: const EdgeInsets.all(12),
                              itemCount: notifier.invoices.length,
                              itemBuilder: (context, index) {
                                final inv = notifier.invoices[index];
                                final id = inv['id'] as int;
                                final code = inv['invoice_code'] ?? 'INV-...';
                                final finalAmount = inv['final_amount'] ?? 0.0;
                                final dateStr = inv['created_at'] ?? '';
                                final status = inv['payment_status'] ?? 'pending';

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
                                        '/invoice-detail',
                                        arguments: id,
                                      ).then((_) => _loadInvoices(page: _currentPage));
                                    },
                                    child: Padding(
                                      padding: const EdgeInsets.all(16.0),
                                      child: Column(
                                        children: [
                                          Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Row(
                                                children: [
                                                  const Icon(Icons.receipt_rounded, color: AppTheme.primaryColor, size: 18),
                                                  const SizedBox(width: 6),
                                                  Text(
                                                    code,
                                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
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
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Text(
                                                    _formatDateTime(dateStr),
                                                    style: TextStyle(color: Colors.grey.shade500, fontSize: 12),
                                                  ),
                                                  const SizedBox(height: 4),
                                                  const Text(
                                                    'Cần thanh toán:',
                                                    style: TextStyle(color: Colors.black87, fontSize: 12, fontWeight: FontWeight.w500),
                                                  ),
                                                ],
                                              ),
                                              Text(
                                                _formatCurrency(finalAmount),
                                                style: TextStyle(
                                                  fontSize: 16,
                                                  fontWeight: FontWeight.bold,
                                                  color: status == 'pending' ? AppTheme.errorColor : Colors.green,
                                                ),
                                              ),
                                            ],
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
                    onPressed: _currentPage > 1 ? () => _loadInvoices(page: _currentPage - 1) : null,
                  ),
                  Text('Trang $_currentPage / ${notifier.totalPages}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  IconButton(
                    icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
                    onPressed: _currentPage < notifier.totalPages ? () => _loadInvoices(page: _currentPage + 1) : null,
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
