import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import 'record_notifier.dart';

class RecordDetailScreen extends StatefulWidget {
  final int recordId;

  const RecordDetailScreen({super.key, required this.recordId});

  @override
  State<RecordDetailScreen> createState() => _RecordDetailScreenState();
}

class _RecordDetailScreenState extends State<RecordDetailScreen> {
  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  void _loadDetail() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<RecordNotifier>(context, listen: false).loadRecordDetail(widget.recordId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<RecordNotifier>(context);
    final rec = notifier.selectedRecordDetail;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Chi Tiết Bệnh Án'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading && rec == null
          ? const LoadingWidget(message: 'Đang tải chi tiết bệnh án...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: _loadDetail)
              : rec == null
                  ? const Center(child: Text('Bệnh án không tồn tại.'))
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Card Chẩn đoán
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(20.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        'Hồ sơ khám ngày ${_formatDate(rec['record_date'] ?? '')}',
                                        style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                      ),
                                      const Icon(Icons.assignment_turned_in_rounded, color: AppTheme.primaryColor),
                                    ],
                                  ),
                                  const Divider(height: 24),
                                  _buildContentBlock('Chẩn đoán của Bác sĩ:', rec['diagnosis'] ?? 'Chưa ghi nhận'),
                                  const SizedBox(height: 16),
                                  _buildContentBlock('Triệu chứng lâm sàng:', rec['symptoms'] ?? 'Chưa ghi nhận'),
                                  const SizedBox(height: 16),
                                  _buildContentBlock('Phác đồ điều trị:', rec['treatment_plan'] ?? 'Chưa ghi nhận'),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Card Chỉ số sinh hiệu (Vitals Card)
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
                                    'Chỉ số sinh hiệu đo được',
                                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15, color: AppTheme.primaryColor),
                                  ),
                                  const Divider(height: 24),
                                  
                                  // Grid hiển thị 4 sinh hiệu chính
                                  GridView.count(
                                    shrinkWrap: true,
                                    physics: const NeverScrollableScrollPhysics(),
                                    crossAxisCount: 2,
                                    crossAxisSpacing: 16,
                                    mainAxisSpacing: 16,
                                    childAspectRatio: 1.8,
                                    children: [
                                      _buildVitalBox(Icons.favorite_rounded, 'Huyết áp', rec['bp'] ?? '--/-- mmHg', Colors.red.shade600),
                                      _buildVitalBox(Icons.thermostat_rounded, 'Nhiệt độ', rec['temperature'] ?? '-- °C', Colors.orange.shade600),
                                      _buildVitalBox(Icons.monitor_weight_rounded, 'Cân nặng', rec['weight'] ?? '-- kg', Colors.blue.shade600),
                                      _buildVitalBox(Icons.height_rounded, 'Chiều cao', rec['height'] ?? '-- cm', Colors.green.shade600),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Card Bác sĩ & Khoa phòng
                          Card(
                            margin: EdgeInsets.zero,
                            color: Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                children: [
                                  _buildDocRow('Bác sĩ điều trị:', rec['doctor_name'] ?? 'Bác sĩ'),
                                  const Divider(height: 16),
                                  _buildDocRow('Khoa phòng khám:', rec['department_name'] ?? 'Khoa'),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
    );
  }

  Widget _buildContentBlock(String title, String content) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: const TextStyle(fontSize: 12, color: Colors.black54, fontWeight: FontWeight.w600),
        ),
        const SizedBox(height: 4),
        Text(
          content,
          style: const TextStyle(fontSize: 14, color: Colors.black87, fontWeight: FontWeight.bold, height: 1.4),
        ),
      ],
    );
  }

  Widget _buildVitalBox(IconData icon, String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withOpacity(0.06),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.2)),
      ),
      child: Row(
        children: [
          Icon(icon, color: color, size: 28),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(label, style: const TextStyle(fontSize: 11, color: Colors.black54)),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: color),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDocRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 13, color: Colors.black54)),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87)),
      ],
    );
  }

  String _formatDate(String dateStr) {
    if (dateStr.isEmpty) return '';
    final parsed = DateTime.tryParse(dateStr);
    if (parsed == null) return dateStr;
    return DateFormat('dd/MM/yyyy').format(parsed);
  }
}
