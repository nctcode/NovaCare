import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import '../departments/departments_notifier.dart';
import 'doctors_notifier.dart';

class DoctorsScreen extends StatefulWidget {
  const DoctorsScreen({super.key});

  @override
  State<DoctorsScreen> createState() => _DoctorsScreenState();
}

class _DoctorsScreenState extends State<DoctorsScreen> {
  final _searchController = TextEditingController();
  int? _departmentId;
  bool _isInit = true;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_isInit) {
      // Nhận tham số departmentId truyền vào nếu có
      final args = ModalRoute.of(context)!.settings.arguments;
      if (args is int) {
        _departmentId = args;
      }
      _loadInitialData();
      _isInit = false;
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _loadInitialData() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<DepartmentsNotifier>(context, listen: false).loadDepartments();
      _loadDoctors();
    });
  }

  void _loadDoctors() {
    Provider.of<DoctorsNotifier>(context, listen: false).loadDoctors(
      departmentId: _departmentId,
      keyword: _searchController.text,
    );
  }

  @override
  Widget build(BuildContext context) {
    final doctorsNotifier = Provider.of<DoctorsNotifier>(context);
    final deptsNotifier = Provider.of<DepartmentsNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Đội Ngũ Bác Sĩ'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // 1. Thanh tìm kiếm & Nút Sort
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Tìm theo tên, chuyên khoa...',
                      prefixIcon: const Icon(Icons.search_rounded),
                      contentPadding: const EdgeInsets.symmetric(vertical: 12),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear_rounded),
                              onPressed: () {
                                _searchController.clear();
                                _loadDoctors();
                              },
                            )
                          : null,
                    ),
                    onChanged: (val) {
                      setState(() {});
                    },
                    onSubmitted: (_) => _loadDoctors(),
                  ),
                ),
                const SizedBox(width: 8),
                IconButton(
                  style: IconButton.styleFrom(
                    backgroundColor: AppTheme.primaryColor,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    padding: const EdgeInsets.all(12),
                  ),
                  icon: const Icon(Icons.search_rounded),
                  onPressed: _loadDoctors,
                ),
                const SizedBox(width: 8),
                // Nút sắp xếp
                PopupMenuButton<String>(
                  icon: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade300),
                    ),
                    child: const Icon(Icons.sort_rounded, color: AppTheme.primaryColor, size: 20),
                  ),
                  tooltip: 'Sắp xếp danh sách',
                  onSelected: (String value) {
                    doctorsNotifier.setSortType(value);
                  },
                  itemBuilder: (BuildContext context) => <PopupMenuEntry<String>>[
                    PopupMenuItem<String>(
                      value: 'name_asc',
                      child: Row(
                        children: [
                          Icon(Icons.sort_by_alpha_rounded, 
                              color: doctorsNotifier.sortType == 'name_asc' ? AppTheme.primaryColor : Colors.grey),
                          const SizedBox(width: 8),
                          const Text('Tên bác sĩ: A -> Z'),
                        ],
                      ),
                    ),
                    PopupMenuItem<String>(
                      value: 'name_desc',
                      child: Row(
                        children: [
                          Icon(Icons.sort_by_alpha_rounded, 
                              color: doctorsNotifier.sortType == 'name_desc' ? AppTheme.primaryColor : Colors.grey),
                          const SizedBox(width: 8),
                          const Text('Tên bác sĩ: Z -> A'),
                        ],
                      ),
                    ),
                    PopupMenuItem<String>(
                      value: 'exp_desc',
                      child: Row(
                        children: [
                          Icon(Icons.workspace_premium_rounded, 
                              color: doctorsNotifier.sortType == 'exp_desc' ? AppTheme.primaryColor : Colors.grey),
                          const SizedBox(width: 8),
                          const Text('Kinh nghiệm nhiều nhất'),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 2. Bộ lọc khoa phòng dạng Chips cuộn ngang
          if (deptsNotifier.isLoading)
            const SizedBox(
              height: 48,
              child: Center(child: SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2))),
            )
          else
            SizedBox(
              height: 48,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                itemCount: deptsNotifier.departments.length + 1,
                itemBuilder: (context, index) {
                  if (index == 0) {
                    final isSelected = _departmentId == null;
                    return Padding(
                      padding: const EdgeInsets.only(right: 8.0),
                      child: ChoiceChip(
                        label: const Text('Tất cả khoa'),
                        selected: isSelected,
                        selectedColor: AppTheme.primaryColor.withOpacity(0.15),
                        backgroundColor: Colors.white,
                        labelStyle: TextStyle(
                          color: isSelected ? AppTheme.primaryColor : Colors.grey.shade700,
                          fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                          fontSize: 12,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(20),
                          side: BorderSide(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade300),
                        ),
                        showCheckmark: false,
                        onSelected: (selected) {
                          if (selected) {
                            setState(() {
                              _departmentId = null;
                            });
                            _loadDoctors();
                          }
                        },
                      ),
                    );
                  }

                  final dept = deptsNotifier.departments[index - 1];
                  final deptId = dept['id'] as int;
                  final deptName = dept['name'] ?? 'Khoa';
                  final isSelected = _departmentId == deptId;

                  return Padding(
                    padding: const EdgeInsets.only(right: 8.0),
                    child: ChoiceChip(
                      label: Text(deptName),
                      selected: isSelected,
                      selectedColor: AppTheme.primaryColor.withOpacity(0.15),
                      backgroundColor: Colors.white,
                      labelStyle: TextStyle(
                        color: isSelected ? AppTheme.primaryColor : Colors.grey.shade700,
                        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                        fontSize: 12,
                      ),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                        side: BorderSide(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade300),
                      ),
                      showCheckmark: false,
                      onSelected: (selected) {
                        setState(() {
                          _departmentId = selected ? deptId : null;
                        });
                        _loadDoctors();
                      },
                    ),
                  );
                },
              ),
            ),

          const SizedBox(height: 8),

          // 3. Danh sách kết quả bác sĩ
          Expanded(
            child: doctorsNotifier.isLoading
                ? const LoadingWidget(message: 'Đang tải danh sách bác sĩ...')
                : doctorsNotifier.errorMessage != null
                    ? ErrorView(message: doctorsNotifier.errorMessage!, onRetry: _loadDoctors)
                    : doctorsNotifier.doctors.isEmpty
                        ? const EmptyState(message: 'Không tìm thấy bác sĩ nào phù hợp.')
                        : ListView.builder(
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                            itemCount: doctorsNotifier.doctors.length,
                            itemBuilder: (context, index) {
                              final doc = doctorsNotifier.doctors[index];
                              final name = doc['name'] ?? 'Bác sĩ';
                              final specialty = doc['specialty'] ?? 'Chuyên khoa';
                              final exp = doc['experience_years'] ?? 0;
                              final deptName = doc['department_name'] ?? 'Khoa phòng';
                              final avatar = doc['avatar'];

                              return Card(
                                margin: const EdgeInsets.symmetric(vertical: 6),
                                elevation: 0.5,
                                color: Colors.white,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16),
                                  side: BorderSide(color: Colors.grey.shade100),
                                ),
                                child: Padding(
                                  padding: const EdgeInsets.all(16.0),
                                  child: Row(
                                    children: [
                                      // Avatar tròn
                                      CircleAvatar(
                                        radius: 30,
                                        backgroundColor: const Color(0xffe3f2fd),
                                        backgroundImage: (avatar != null && avatar.toString().isNotEmpty) 
                                            ? NetworkImage(avatar) 
                                            : null,
                                        child: (avatar == null || avatar.toString().isEmpty)
                                            ? const Icon(Icons.person_rounded, color: AppTheme.primaryColor, size: 36)
                                            : null,
                                      ),
                                      const SizedBox(width: 16),
                                      
                                      // Thông tin chi tiết (Email, phone cá nhân, status ẩn hoàn toàn)
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              name,
                                              style: const TextStyle(
                                                fontWeight: FontWeight.bold,
                                                fontSize: 15,
                                                color: Colors.black87,
                                              ),
                                            ),
                                            const SizedBox(height: 4),
                                            Text(
                                              specialty,
                                              style: TextStyle(
                                                color: Colors.grey.shade600,
                                                fontSize: 12,
                                                fontWeight: FontWeight.w500,
                                              ),
                                            ),
                                            const SizedBox(height: 2),
                                            Text(
                                              'Khoa: $deptName',
                                              style: TextStyle(
                                                color: Colors.grey.shade500,
                                                fontSize: 12,
                                              ),
                                            ),
                                            const SizedBox(height: 6),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                              decoration: BoxDecoration(
                                                color: const Color(0xffe8f5e9),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                'Kinh nghiệm: $exp năm',
                                                style: const TextStyle(
                                                  color: Colors.green,
                                                  fontSize: 11,
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      
                                      // Nút hành động đặt lịch
                                      IconButton(
                                        style: IconButton.styleFrom(
                                          backgroundColor: AppTheme.primaryColor.withOpacity(0.1),
                                          foregroundColor: AppTheme.primaryColor,
                                        ),
                                        icon: const Icon(Icons.calendar_month_rounded, size: 20),
                                        onPressed: () {
                                          Navigator.pushNamed(
                                            context,
                                            '/book-appointment',
                                            arguments: doc,
                                          );
                                        },
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
          ),
        ],
      ),
    );
  }
}
