import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'record_notifier.dart';

class MedicalRecordsScreen extends StatefulWidget {
  const MedicalRecordsScreen({super.key});

  @override
  State<MedicalRecordsScreen> createState() => _MedicalRecordsScreenState();
}

class _MedicalRecordsScreenState extends State<MedicalRecordsScreen> {
  int _currentPage = 1;

  @override
  void initState() {
    super.initState();
    _loadRecords();
  }

  void _loadRecords({int page = 1}) {
    _currentPage = page;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<RecordNotifier>(context, listen: false).loadRecords(page: _currentPage);
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<RecordNotifier>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Bệnh Án Y Khoa'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && notifier.records.isEmpty
          ? const LoadingWidget(message: 'Đang tải lịch sử bệnh án...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: () => _loadRecords(page: _currentPage))
              : notifier.records.isEmpty
                  ? const EmptyState(
                      message: 'Bạn chưa có hồ sơ bệnh án nào tại bệnh viện.',
                      icon: Icons.assignment_outlined,
                    )
                  : Column(
                      children: [
                        Expanded(
                          child: ListView.builder(
                            padding: const EdgeInsets.all(12),
                            itemCount: notifier.records.length,
                            itemBuilder: (context, index) {
                              final rec = notifier.records[index];
                              final id = rec['id'] as int;
                              final dateStr = rec['record_date'] ?? '';
                              final diagnosis = rec['diagnosis'] ?? 'Chưa chẩn đoán';
                              final treatment = rec['treatment_plan'] ?? 'Chưa có phác đồ';
                              final doctor = rec['doctor_name'] ?? 'Bác sĩ';

                              DateTime? recDate;
                              if (dateStr.isNotEmpty) {
                                recDate = DateTime.tryParse(dateStr);
                              }
                              final formattedDate = recDate != null
                                  ? DateFormat('dd/MM/yyyy').format(recDate)
                                  : dateStr;

                              return Card(
                                margin: const EdgeInsets.symmetric(vertical: 6),
                                color: Colors.white,
                                child: InkWell(
                                  borderRadius: BorderRadius.circular(12),
                                  onTap: () {
                                    Navigator.pushNamed(
                                      context,
                                      '/record-detail',
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
                                              'Bệnh án ngày $formattedDate',
                                              style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                            ),
                                            const Icon(Icons.chevron_right_rounded, color: Colors.black38),
                                          ],
                                        ),
                                        const Divider(height: 20),
                                        Text(
                                          'Chẩn đoán: $diagnosis',
                                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                        ),
                                        const SizedBox(height: 6),
                                        Text(
                                          'Phác đồ: $treatment',
                                          style: const TextStyle(fontSize: 13, color: Colors.black54),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 8),
                                        Text(
                                          'Bác sĩ điều trị: $doctor',
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
                                  onPressed: _currentPage > 1 ? () => _loadRecords(page: _currentPage - 1) : null,
                                ),
                                Text('Trang $_currentPage / ${notifier.totalPages}', style: const TextStyle(fontWeight: FontWeight.bold)),
                                IconButton(
                                  icon: const Icon(Icons.arrow_forward_ios_rounded, size: 18),
                                  onPressed: _currentPage < notifier.totalPages ? () => _loadRecords(page: _currentPage + 1) : null,
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
    );
  }
}
