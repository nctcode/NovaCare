import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'prescription_notifier.dart';

class PrescriptionsScreen extends StatefulWidget {
  const PrescriptionsScreen({super.key});

  @override
  State<PrescriptionsScreen> createState() => _PrescriptionsScreenState();
}

class _PrescriptionsScreenState extends State<PrescriptionsScreen> {
  int _currentPage = 1;

  @override
  void initState() {
    super.initState();
    _loadPrescriptions();
  }

  void _loadPrescriptions({int page = 1}) {
    _currentPage = page;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<PrescriptionNotifier>(context, listen: false).loadPrescriptions(page: _currentPage);
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<PrescriptionNotifier>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Đơn Thuốc Của Tôi'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && notifier.prescriptions.isEmpty
          ? const LoadingWidget(message: 'Đang tải danh sách đơn thuốc...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: () => _loadPrescriptions(page: _currentPage))
              : notifier.prescriptions.isEmpty
                  ? const EmptyState(
                      message: 'Bạn chưa có đơn thuốc nào tại hệ thống.',
                      icon: Icons.medication_liquid_rounded,
                    )
                  : Column(
                      children: [
                        Expanded(
                          child: ListView.builder(
                            padding: const EdgeInsets.all(12),
                            itemCount: notifier.prescriptions.length,
                            itemBuilder: (context, index) {
                              final pr = notifier.prescriptions[index];
                              final id = pr['id'] as int;
                              final dateStr = pr['prescription_date'] ?? '';
                              final notes = pr['notes'] ?? 'Uống thuốc theo chỉ định';
                              final doctor = pr['doctor_name'] ?? 'Bác sĩ';

                              DateTime? prDate;
                              if (dateStr.isNotEmpty) {
                                prDate = DateTime.tryParse(dateStr);
                              }
                              final formattedDate = prDate != null
                                  ? DateFormat('dd/MM/yyyy').format(prDate)
                                  : dateStr;

                              return Card(
                                margin: const EdgeInsets.symmetric(vertical: 6),
                                color: Colors.white,
                                child: InkWell(
                                  borderRadius: BorderRadius.circular(12),
                                  onTap: () {
                                    Navigator.pushNamed(
                                      context,
                                      '/prescription-detail',
                                      arguments: id,
                                    );
                                  },
                                  child: Padding(
                                    padding: const EdgeInsets.all(16.0),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              'Đơn thuốc ngày $formattedDate',
                                              style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                            ),
                                            const Icon(Icons.chevron_right_rounded, color: Colors.black38),
                                          ],
                                        ),
                                        const Divider(height: 20),
                                        Text(
                                          'Ghi chú: $notes',
                                          style: const TextStyle(fontSize: 13, color: Colors.black87),
                                        ),
                                        const SizedBox(height: 8),
                                        Text(
                                          'Bác sĩ kê đơn: $doctor',
                                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.primaryColor),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                        if (notifier.totalPages > 1)
                          Container(
                            padding: const EdgeInsets.symmetric(vertical: 8),
                            color: Colors.white,
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                IconButton(
                                  icon: const Icon(Icons.arrow_back_ios_rounded, size: 18),
                                  onPressed: _currentPage > 1 ? () => _loadPrescriptions(page: _currentPage - 1) : null,
                                ),
                                Text('Trang $_currentPage / ${notifier.totalPages}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                IconButton(
                                  icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
                                  onPressed: _currentPage < notifier.totalPages ? () => _loadPrescriptions(page: _currentPage + 1) : null,
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
    );
  }
}
