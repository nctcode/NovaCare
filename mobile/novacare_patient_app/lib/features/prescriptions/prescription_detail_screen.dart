import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import 'prescription_notifier.dart';

class PrescriptionDetailScreen extends StatefulWidget {
  final int prescriptionId;

  const PrescriptionDetailScreen({super.key, required this.prescriptionId});

  @override
  State<PrescriptionDetailScreen> createState() => _PrescriptionDetailScreenState();
}

class _PrescriptionDetailScreenState extends State<PrescriptionDetailScreen> {
  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  void _loadDetail() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<PrescriptionNotifier>(context, listen: false)
          .loadPrescriptionDetail(widget.prescriptionId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<PrescriptionNotifier>(context);
    final pr = notifier.selectedPrescriptionDetail;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Chi Tiết Đơn Thuốc'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && pr == null
          ? const LoadingWidget(message: 'Đang tải chi tiết đơn thuốc...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: _loadDetail)
              : pr == null
                  ? const Center(child: Text('Đơn thuốc không tồn tại.'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Card thông tin đơn thuốc chung
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        'Đơn thuốc ngày ${_formatDate(pr['prescription_date'] ?? '')}',
                                        style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                      ),
                                      const Icon(Icons.medication_rounded, color: AppTheme.primaryColor),
                                    ],
                                  ),
                                  const Divider(height: 24),
                                  _buildInfoText('Bệnh nhân:', pr['patient_name'] ?? 'Bệnh nhân'),
                                  const SizedBox(height: 8),
                                  _buildInfoText('Bác sĩ kê đơn:', pr['doctor_name'] ?? 'Bác sĩ'),
                                  const SizedBox(height: 8),
                                  _buildInfoText('Chỉ dẫn / Ghi chú:', pr['notes'] ?? 'Uống thuốc theo hướng dẫn sử dụng.'),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Tiêu đề danh sách thuốc
                          const Padding(
                            padding: EdgeInsets.symmetric(horizontal: 4.0, vertical: 8.0),
                            child: Text(
                              'Danh Sách Thuốc Chỉ Định',
                              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87),
                            ),
                          ),

                          // Danh sách thuốc chi tiết
                          ListView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: (pr['items'] as List?)?.length ?? 0,
                            itemBuilder: (context, index) {
                              final item = pr['items'][index];
                              final medName = item['medicine_name'] ?? 'Biệt dược';
                              final qty = item['quantity'] ?? 0;
                              final dosage = item['dosage'] ?? 'Theo chỉ định';
                              final duration = item['duration'] ?? 'Chưa rõ';
                              final frequency = item['frequency'] ?? '';
                              final instructions = item['instructions'] ?? 'Không có ghi chú thêm';

                              return Card(
                                margin: const EdgeInsets.symmetric(vertical: 6),
                                color: Colors.white,
                                child: Padding(
                                  padding: const EdgeInsets.all(16.0),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Expanded(
                                            child: Text(
                                              medName,
                                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.primaryColor),
                                            ),
                                          ),
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                            decoration: BoxDecoration(
                                              color: Colors.grey.shade100,
                                              borderRadius: BorderRadius.circular(8),
                                            ),
                                            child: Text(
                                              'SL: $qty',
                                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                                            ),
                                          ),
                                        ],
                                      ),
                                      const Divider(height: 20),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          _buildMedUsageItem('Liều lượng:', dosage),
                                          _buildMedUsageItem('Thời gian uống:', duration),
                                        ],
                                      ),
                                      if (frequency.isNotEmpty) ...[
                                        const SizedBox(height: 8),
                                        _buildMedUsageItem('Tần suất:', frequency),
                                      ],
                                      const SizedBox(height: 8),
                                      _buildMedUsageItem('Hướng dẫn:', instructions, isFullWidth: true),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                    ),
    );
  }

  Widget _buildInfoText(String label, String value) {
    return RichText(
      text: TextSpan(
        style: const TextStyle(fontSize: 14, color: Colors.black87, height: 1.4),
        children: [
          TextSpan(text: '$label ', style: const TextStyle(color: Colors.black54)),
          TextSpan(text: value, style: const TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildMedUsageItem(String label, String value, {bool isFullWidth = false}) {
    final content = RichText(
      text: TextSpan(
        style: const TextStyle(fontSize: 13, color: Colors.black87),
        children: [
          TextSpan(text: '$label ', style: const TextStyle(color: Colors.black54)),
          TextSpan(text: value, style: const TextStyle(fontWeight: FontWeight.bold)),
        ],
      ),
    );

    if (isFullWidth) {
      return content;
    }

    return Flexible(child: content);
  }

  String _formatDate(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('dd/MM/yyyy').format(parsed);
  }
}


