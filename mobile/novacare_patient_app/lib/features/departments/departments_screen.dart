import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import 'departments_notifier.dart';

class DepartmentsScreen extends StatefulWidget {
  const DepartmentsScreen({super.key});

  @override
  State<DepartmentsScreen> createState() => _DepartmentsScreenState();
}

class _DepartmentsScreenState extends State<DepartmentsScreen> {
  @override
  void initState() {
    super.initState();
    _loadData();
  }

  void _loadData() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<DepartmentsNotifier>(context, listen: false).loadDepartments();
    });
  }

  @override
  Widget build(BuildContext context) {
    final notifier = Provider.of<DepartmentsNotifier>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Khoa Phòng Khám'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: notifier.isLoading
          ? const LoadingWidget(message: 'Đang tải danh sách khoa phòng...')
          : notifier.errorMessage != null
              ? ErrorView(message: notifier.errorMessage!, onRetry: _loadData)
              : notifier.departments.isEmpty
                  ? const EmptyState(message: 'Chưa có thông tin khoa phòng nào.')
                  : ListView.builder(
                      padding: const EdgeInsets.all(12),
                      itemCount: notifier.departments.length,
                      itemBuilder: (context, index) {
                        final dept = notifier.departments[index];
                        return Card(
                          margin: const EdgeInsets.symmetric(vertical: 6),
                          elevation: 1,
                          color: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          child: ListTile(
                            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                            leading: const CircleAvatar(
                              backgroundColor: Color(0xffe3f2fd),
                              child: Icon(Icons.health_and_safety_rounded, color: AppTheme.primaryColor),
                            ),
                            title: Text(
                              dept['name'] ?? 'Khoa phòng',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                            ),
                            subtitle: Padding(
                              padding: const EdgeInsets.only(top: 4.0),
                              child: Text(
                                dept['description'] ?? 'Không có mô tả khoa phòng.',
                                style: const TextStyle(color: Colors.black54, fontSize: 13),
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            trailing: const Icon(Icons.chevron_right_rounded, color: Colors.black38),
                            onTap: () {
                              // Chuyển sang danh sách bác sĩ lọc theo department_id
                              Navigator.pushNamed(
                                context,
                                '/doctors',
                                arguments: dept['id'] as int,
                              );
                            },
                          ),
                        );
                      },
                    ),
    );
  }
}
