import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import '../../core/widgets/empty_state.dart';
import '../departments/departments_notifier.dart';
import '../doctors/doctors_notifier.dart';
import 'appointment_notifier.dart';

class BookAppointmentScreen extends StatefulWidget {
  const BookAppointmentScreen({super.key});

  @override
  State<BookAppointmentScreen> createState() => _BookAppointmentScreenState();
}

class _BookAppointmentScreenState extends State<BookAppointmentScreen> {
  final _formKey = GlobalKey<FormState>();
  final _reasonController = TextEditingController();
  
  int _currentStep = 0; // Steps: 0: Khoa, 1: Bác sĩ, 2: Ngày, 3: Giờ, 4: Lý do & Xác nhận
  
  Map<String, dynamic>? _selectedDepartment;
  Map<String, dynamic>? _selectedDoctor;
  DateTime? _selectedDate;
  String? _selectedSlot;
  bool _isInit = true;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_isInit) {
      final args = ModalRoute.of(context)!.settings.arguments;
      // Tải sẵn danh sách khoa
      Provider.of<DepartmentsNotifier>(context, listen: false).loadDepartments();
      
      if (args is Map<String, dynamic>) {
        _selectedDoctor = args;
        // Bác sĩ đã được truyền sẵn từ danh sách bác sĩ
        if (_selectedDoctor!['department_ids'] != null && (_selectedDoctor!['department_ids'] as List).isNotEmpty) {
          final deptId = (_selectedDoctor!['department_ids'] as List).first as int;
          // Tìm khoa tương ứng
          final deptsNotifier = Provider.of<DepartmentsNotifier>(context, listen: false);
          _selectedDepartment = deptsNotifier.departments.firstWhere(
            (d) => d['id'] == deptId,
            orElse: () => {'id': deptId, 'name': _selectedDoctor!['department_name'] ?? 'Chuyên khoa'},
          );
        }
        _currentStep = 2; // Bỏ qua bước 0 và 1, nhảy đến chọn ngày
      }
      _isInit = false;
    }
  }

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  void _loadDoctorsInDept(int deptId) {
    Provider.of<DoctorsNotifier>(context, listen: false).loadDoctors(departmentId: deptId);
  }

  void _loadSlots() {
    if (_selectedDoctor == null || _selectedDate == null) return;
    final dateStr = DateFormat('yyyy-MM-dd').format(_selectedDate!);
    final doctorId = _selectedDoctor!['id'] as int;

    Provider.of<AppointmentNotifier>(context, listen: false)
        .loadAvailableSlots(doctorId, dateStr);
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now().add(const Duration(days: 1)),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );
    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
        _selectedSlot = null; // reset slot khi chọn ngày mới
      });
      _loadSlots();
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: color,
      ),
    );
  }

  // Màn hình/Bottom Sheet tóm tắt xác nhận đặt lịch
  void _showConfirmationBottomSheet() {
    if (_selectedDoctor == null || _selectedDepartment == null || _selectedDate == null || _selectedSlot == null) {
      _showSnackBar('Vui lòng hoàn thành tất cả các bước.', AppTheme.errorColor);
      return;
    }

    final dateStr = DateFormat('dd/MM/yyyy').format(_selectedDate!);
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) {
        return Padding(
          padding: EdgeInsets.fromLTRB(20, 20, 20, MediaQuery.of(context).viewInsets.bottom + 20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Row(
                children: [
                  Icon(Icons.assignment_turned_in_rounded, color: AppTheme.primaryColor, size: 28),
                  SizedBox(width: 10),
                  Text(
                    'Xác Nhận Lịch Hẹn',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.black87),
                  ),
                ],
              ),
              const Divider(height: 24),
              
              _buildConfirmRow('Khoa khám:', _selectedDepartment!['name'] ?? ''),
              const SizedBox(height: 8),
              _buildConfirmRow('Bác sĩ:', _selectedDoctor!['name'] ?? ''),
              const SizedBox(height: 8),
              _buildConfirmRow('Ngày khám:', dateStr),
              const SizedBox(height: 8),
              _buildConfirmRow('Giờ khám:', _selectedSlot!),
              const SizedBox(height: 8),
              _buildConfirmRow('Lý do khám:', _reasonController.text.trim()),
              const Divider(height: 28),
              
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: () {
                  Navigator.pop(context); // đóng bottom sheet
                  _submitBooking();
                },
                child: const Text('XÁC NHẬN & ĐẶT LỊCH'),
              ),
              const SizedBox(height: 8),
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('HỦY BỎ', style: TextStyle(color: Colors.black54)),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildConfirmRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 90,
          child: Text(
            label,
            style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black54, fontSize: 13),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.black87, fontSize: 13),
          ),
        ),
      ],
    );
  }

  void _submitBooking() async {
    final notifier = Provider.of<AppointmentNotifier>(context, listen: false);
    final dateStr = DateFormat('yyyy-MM-dd').format(_selectedDate!);
    final doctorId = _selectedDoctor!['id'] as int;
    final deptId = _selectedDepartment!['id'] as int;

    final success = await notifier.bookAppointment(
      doctorId: doctorId,
      departmentId: deptId,
      date: dateStr,
      time: _selectedSlot!,
      reason: _reasonController.text.trim(),
    );

    if (!mounted) return;

    if (success) {
      _showSuccessDialog();
    } else {
      String userFriendlyError = notifier.errorMessage ?? 'Không thể đặt lịch khám.';
      // Bắt lỗi trùng giờ 409
      if (userFriendlyError.contains('409') || userFriendlyError.contains('trùng') || userFriendlyError.contains('đã có người đặt')) {
        userFriendlyError = 'Khung giờ này vừa có người đặt. Vui lòng chọn khung giờ khác.';
      }
      _showSnackBar(userFriendlyError, AppTheme.errorColor);
    }
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.check_circle_rounded, color: Colors.green, size: 28),
              SizedBox(width: 8),
              Text('Đặt lịch thành công'),
            ],
          ),
          content: const Text(
            'Lịch hẹn khám của bạn đã được ghi nhận thành công và đang chờ xác nhận từ bệnh viện.',
          ),
          actions: [
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                Navigator.pop(context);
                Navigator.pushReplacementNamed(context, '/my-appointments');
              },
              child: const Text('XEM LỊCH HẸN'),
            ),
          ],
        );
      },
    );
  }

  // Quay lại bước trước
  void _prevStep() {
    if (_currentStep > 0) {
      setState(() {
        _currentStep--;
      });
    }
  }

  // Tiến lên bước sau
  void _nextStep() {
    if (_currentStep == 0 && _selectedDepartment == null) {
      _showSnackBar('Vui lòng chọn khoa phòng.', AppTheme.errorColor);
      return;
    }
    if (_currentStep == 1 && _selectedDoctor == null) {
      _showSnackBar('Vui lòng chọn bác sĩ.', AppTheme.errorColor);
      return;
    }
    if (_currentStep == 2 && _selectedDate == null) {
      _showSnackBar('Vui lòng chọn ngày khám.', AppTheme.errorColor);
      return;
    }
    if (_currentStep == 3 && _selectedSlot == null) {
      _showSnackBar('Vui lòng chọn khung giờ khám.', AppTheme.errorColor);
      return;
    }
    if (_currentStep == 4) {
      if (!_formKey.currentState!.validate()) return;
      _showConfirmationBottomSheet();
      return;
    }

    setState(() {
      _currentStep++;
    });

    if (_currentStep == 1) {
      _loadDoctorsInDept(_selectedDepartment!['id'] as int);
    }
  }

  @override
  Widget build(BuildContext context) {
    final deptsNotifier = Provider.of<DepartmentsNotifier>(context);
    final doctorsNotifier = Provider.of<DoctorsNotifier>(context);
    final apptNotifier = Provider.of<AppointmentNotifier>(context);

    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Đặt Lịch Hẹn Khám'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Chỉ báo các bước (Progress Indicator Wizard)
          _buildWizardProgress(),
          
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Card(
                color: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: _buildStepContent(deptsNotifier, doctorsNotifier, apptNotifier),
                ),
              ),
            ),
          ),
          
          // Phím điều hướng dưới cùng
          _buildWizardNavigation(),
        ],
      ),
    );
  }

  // Widget hiển thị thanh tiến trình 5 bước
  Widget _buildWizardProgress() {
    final stepLabels = ['Khoa', 'Bác sĩ', 'Ngày', 'Giờ', 'Lý do'];
    
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
      color: Colors.white,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: List.generate(stepLabels.length, (index) {
          final isCompleted = index < _currentStep;
          final isCurrent = index == _currentStep;
          
          Color circleColor = Colors.grey.shade200;
          Color textColor = Colors.black45;
          if (isCompleted) {
            circleColor = Colors.green;
            textColor = Colors.green;
          } else if (isCurrent) {
            circleColor = AppTheme.primaryColor;
            textColor = AppTheme.primaryColor;
          }
          
          return Row(
            children: [
              Column(
                children: [
                  CircleAvatar(
                    radius: 12,
                    backgroundColor: circleColor,
                    child: isCompleted
                        ? const Icon(Icons.check, size: 14, color: Colors.white)
                        : Text(
                            '${index + 1}',
                            style: TextStyle(
                              color: isCurrent ? Colors.white : Colors.black54,
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    stepLabels[index],
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: (isCurrent || isCompleted) ? FontWeight.bold : FontWeight.normal,
                      color: textColor,
                    ),
                  ),
                ],
              ),
              if (index < stepLabels.length - 1)
                Container(
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  width: MediaQuery.of(context).size.width / (stepLabels.length * 2.3),
                  height: 2,
                  color: isCompleted ? Colors.green : Colors.grey.shade200,
                ),
            ],
          );
        }),
      ),
    );
  }

  // Nội dung chi tiết từng bước
  Widget _buildStepContent(
    DepartmentsNotifier deptsNotifier,
    DoctorsNotifier doctorsNotifier,
    AppointmentNotifier apptNotifier,
  ) {
    switch (_currentStep) {
      case 0:
        // Bước 1: Chọn Khoa phòng
        if (deptsNotifier.isLoading) {
          return const LoadingWidget(message: 'Đang tải danh sách khoa phòng...');
        }
        if (deptsNotifier.departments.isEmpty) {
          return const EmptyState(message: 'Không tìm thấy khoa phòng khám bệnh.');
        }
        return Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Chọn Khoa Phòng Khám:',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 12),
            ...deptsNotifier.departments.map((dept) {
              final isSelected = _selectedDepartment?['id'] == dept['id'];
              return Card(
                margin: const EdgeInsets.symmetric(vertical: 4),
                color: isSelected ? AppTheme.primaryColor.withOpacity(0.05) : Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                  side: BorderSide(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade200),
                ),
                child: ListTile(
                  title: Text(
                    dept['name'] ?? '',
                    style: TextStyle(fontWeight: isSelected ? FontWeight.bold : FontWeight.normal),
                  ),
                  trailing: isSelected ? const Icon(Icons.check_circle, color: AppTheme.primaryColor) : null,
                  onTap: () {
                    setState(() {
                      _selectedDepartment = dept;
                      _selectedDoctor = null;
                      _selectedDate = null;
                      _selectedSlot = null;
                    });
                  },
                ),
              );
            }),
          ],
        );

      case 1:
        // Bước 2: Chọn bác sĩ
        if (doctorsNotifier.isLoading) {
          return const LoadingWidget(message: 'Đang tìm kiếm bác sĩ trong khoa...');
        }
        if (doctorsNotifier.doctors.isEmpty) {
          return EmptyState(
            message: 'Không tìm thấy bác sĩ nào thuộc khoa ${_selectedDepartment?['name']}.',
          );
        }
        return Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              'Chọn Bác Sĩ (Khoa ${_selectedDepartment?['name']}):',
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
            ),
            const SizedBox(height: 12),
            ...doctorsNotifier.doctors.map((doc) {
              final isSelected = _selectedDoctor?['id'] == doc['id'];
              final name = doc['name'] ?? '';
              final specialty = doc['specialty'] ?? '';
              final exp = doc['experience_years'] ?? 0;
              final avatar = doc['avatar'];

              return Card(
                margin: const EdgeInsets.symmetric(vertical: 6),
                color: isSelected ? AppTheme.primaryColor.withOpacity(0.05) : Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: BorderSide(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade200),
                ),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: const Color(0xffe3f2fd),
                    backgroundImage: (avatar != null && avatar.toString().isNotEmpty) ? NetworkImage(avatar) : null,
                    child: (avatar == null || avatar.toString().isEmpty)
                        ? const Icon(Icons.person_rounded, color: AppTheme.primaryColor)
                        : null,
                  ),
                  title: Text(
                    name,
                    style: TextStyle(fontWeight: isSelected ? FontWeight.bold : FontWeight.normal),
                  ),
                  subtitle: Text('$specialty - Kinh nghiệm $exp năm'),
                  trailing: isSelected ? const Icon(Icons.check_circle, color: AppTheme.primaryColor) : null,
                  onTap: () {
                    setState(() {
                      _selectedDoctor = doc;
                      _selectedDate = null;
                      _selectedSlot = null;
                    });
                  },
                ),
              );
            }),
          ],
        );

      case 2:
        // Bước 3: Chọn ngày khám
        return Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Chọn Ngày Khám Bệnh:',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: () => _selectDate(context),
              borderRadius: BorderRadius.circular(12),
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.primaryColor),
                  color: AppTheme.primaryColor.withOpacity(0.02),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.calendar_month_rounded, color: AppTheme.primaryColor, size: 28),
                        const SizedBox(width: 12),
                        Text(
                          _selectedDate != null
                              ? DateFormat('dd/MM/yyyy').format(_selectedDate!)
                              : 'Nhấp để chọn ngày khám',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: _selectedDate != null ? Colors.black87 : Colors.grey,
                          ),
                        ),
                      ],
                    ),
                    const Icon(Icons.arrow_drop_down_circle_rounded, color: AppTheme.primaryColor),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),
            if (_selectedDoctor != null) ...[
              Card(
                color: Colors.grey.shade50,
                elevation: 0,
                child: Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: Row(
                    children: [
                      const Icon(Icons.person_rounded, color: AppTheme.primaryColor),
                      const SizedBox(width: 8),
                      Text(
                        'Đang đặt lịch khám với: ${_selectedDoctor!['name']}',
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ],
        );

      case 3:
        // Bước 4: Chọn slot giờ trống
        if (apptNotifier.isLoading && apptNotifier.availableSlots.isEmpty) {
          return const Center(child: CircularProgressIndicator());
        }
        return Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Chọn Khung Giờ Trống Khả Dụng:',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 16),
            apptNotifier.availableSlots.isEmpty
                ? Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.orange.shade50,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Text(
                      'Không có giờ trống nào còn khả dụng trong ngày này. Vui lòng quay lại chọn ngày khác.',
                      style: TextStyle(color: Colors.orange, fontSize: 13),
                      textAlign: TextAlign.center,
                    ),
                  )
                : GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      crossAxisSpacing: 8,
                      mainAxisSpacing: 8,
                      childAspectRatio: 2.2,
                    ),
                    itemCount: apptNotifier.availableSlots.length,
                    itemBuilder: (context, index) {
                      final slot = apptNotifier.availableSlots[index];
                      final isSelected = _selectedSlot == slot;
                      return ChoiceChip(
                        label: Text(slot),
                        selected: isSelected,
                        selectedColor: AppTheme.primaryColor,
                        backgroundColor: Colors.white,
                        labelStyle: TextStyle(
                          color: isSelected ? Colors.white : Colors.black87,
                          fontWeight: FontWeight.bold,
                        ),
                        showCheckmark: false,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                          side: BorderSide(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade300),
                        ),
                        onSelected: (selected) {
                          setState(() {
                            _selectedSlot = selected ? slot : null;
                          });
                        },
                      );
                    },
                  ),
          ],
        );

      case 4:
        // Bước 5: Nhập lý do khám
        return Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text(
                'Mô Tả Triệu Chứng / Lý Do Khám:',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _reasonController,
                maxLines: 4,
                decoration: const InputDecoration(
                  hintText: 'Bé bị ho sốt / Đau nhức vùng vai gáy kéo dài...',
                ),
                validator: (value) {
                  if (value == null || value.trim().isEmpty) {
                    return 'Vui lòng điền lý do khám.';
                  }
                  if (value.trim().length < 5) {
                    return 'Lý do khám phải dài tối thiểu 5 ký tự.';
                  }
                  return null;
                },
              ),
            ],
          ),
        );

      default:
        return const SizedBox();
    }
  }

  // Phím điều hướng ở góc dưới màn hình
  Widget _buildWizardNavigation() {
    final isFirstStep = _currentStep == 0;
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Color(0xffe0e0e0))),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          if (!isFirstStep)
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.grey.shade100,
                foregroundColor: Colors.black87,
                elevation: 0,
              ),
              onPressed: _prevStep,
              child: const Row(
                children: [
                  Icon(Icons.arrow_back_ios_new_rounded, size: 16),
                  SizedBox(width: 8),
                  Text('QUAY LẠI'),
                ],
              ),
            )
          else
            const SizedBox(),
          
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primaryColor,
              foregroundColor: Colors.white,
            ),
            onPressed: _nextStep,
            child: Row(
              children: [
                Text(_currentStep == 4 ? 'XÁC NHẬN' : 'TIẾP TỤC'),
                const SizedBox(width: 8),
                const Icon(Icons.arrow_forward_ios_rounded, size: 16),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
