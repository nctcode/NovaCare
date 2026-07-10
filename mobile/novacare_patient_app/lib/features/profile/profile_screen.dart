import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/loading_widget.dart';
import '../../core/widgets/error_view.dart';
import 'profile_notifier.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  final _emergencyController = TextEditingController();
  final _insuranceController = TextEditingController();
  
  String? _selectedGender;
  String? _selectedBloodType;
  DateTime? _selectedDateOfBirth;

  final List<String> _genders = ['male', 'female', 'other'];
  final List<String> _bloodTypes = ['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-'];

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final notifier = Provider.of<ProfileNotifier>(context, listen: false);
      await notifier.loadProfile();
      
      if (notifier.profile != null) {
        final profile = notifier.profile!;
        _nameController.text = profile['name'] ?? '';
        _phoneController.text = profile['phone'] ?? '';
        _addressController.text = profile['address'] ?? '';
        _emergencyController.text = profile['emergency_contact'] ?? '';
        _insuranceController.text = profile['insurance_number'] ?? '';
        
        if (profile['gender'] != null && _genders.contains(profile['gender'])) {
          _selectedGender = profile['gender'];
        }
        if (profile['blood_type'] != null) {
          _selectedBloodType = profile['blood_type'];
        }
        if (profile['date_of_birth'] != null) {
          _selectedDateOfBirth = DateTime.tryParse(profile['date_of_birth']);
        }
        setState(() {});
      }
    });
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    _emergencyController.dispose();
    _insuranceController.dispose();
    super.dispose();
  }

  // Chọn ngày sinh
  Future<void> _selectDateOfBirth(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedDateOfBirth ?? DateTime(2000),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );
    if (picked != null && picked != _selectedDateOfBirth) {
      setState(() {
        _selectedDateOfBirth = picked;
      });
    }
  }

  void _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    final notifier = Provider.of<ProfileNotifier>(context, listen: false);
    
    final dobStr = _selectedDateOfBirth != null
        ? "${_selectedDateOfBirth!.year}-${_selectedDateOfBirth!.month.toString().padLeft(2, '0')}-${_selectedDateOfBirth!.day.toString().padLeft(2, '0')}"
        : null;

    final success = await notifier.updateProfile(
      name: _nameController.text.trim(),
      phone: _phoneController.text.trim(),
      dateOfBirth: dobStr,
      gender: _selectedGender,
      address: _addressController.text.trim(),
      bloodType: _selectedBloodType,
      emergencyContact: _emergencyController.text.trim(),
      insuranceNumber: _insuranceController.text.trim(),
    );

    if (!mounted) return;

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Cập nhật hồ sơ bệnh nhân thành công!'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(notifier.errorMessage ?? 'Không thể cập nhật hồ sơ.'),
          backgroundColor: AppTheme.errorColor,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final profileNotifier = Provider.of<ProfileNotifier>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Hồ Sơ Cá Nhân'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: profileNotifier.isLoading && profileNotifier.profile == null
          ? const LoadingWidget(message: 'Đang tải hồ sơ bệnh nhân...')
          : profileNotifier.errorMessage != null
              ? ErrorView(message: profileNotifier.errorMessage!, onRetry: _loadProfile)
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16.0),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Card(
                          margin: EdgeInsets.zero,
                          elevation: 1,
                          color: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Thông tin tài khoản (Chỉ xem)',
                                  style: TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                ),
                                const SizedBox(height: 12),
                                _buildReadOnlyRow('Mã Bệnh nhân:', 'PT-${profileNotifier.profile?['id'] ?? '...'}'),
                                _buildReadOnlyRow('Tên Đăng Nhập (Email):', profileNotifier.profile?['email'] ?? '...'),
                                const SizedBox(height: 8),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Card(
                          margin: EdgeInsets.zero,
                          elevation: 1,
                          color: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Thông tin cá nhân & Sức khỏe',
                                  style: TextStyle(fontWeight: FontWeight.bold, color: AppTheme.primaryColor),
                                ),
                                const SizedBox(height: 16),

                                // Name Input
                                TextFormField(
                                  controller: _nameController,
                                  decoration: const InputDecoration(
                                    labelText: 'Họ và tên',
                                    prefixIcon: Icon(Icons.person_outline_rounded),
                                  ),
                                  validator: (value) {
                                    if (value == null || value.trim().isEmpty) {
                                      return 'Vui lòng nhập họ và tên.';
                                    }
                                    return null;
                                  },
                                ),
                                const SizedBox(height: 16),

                                // Phone Input
                                TextFormField(
                                  controller: _phoneController,
                                  decoration: const InputDecoration(
                                    labelText: 'Số điện thoại',
                                    prefixIcon: Icon(Icons.phone_outlined),
                                  ),
                                  validator: (value) {
                                    if (value == null || value.trim().isEmpty) {
                                      return 'Vui lòng nhập số điện thoại.';
                                    }
                                    return null;
                                  },
                                ),
                                const SizedBox(height: 16),

                                // Date of Birth Input (Date picker trigger)
                                InkWell(
                                  onTap: () => _selectDateOfBirth(context),
                                  borderRadius: BorderRadius.circular(10),
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
                                    decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(10),
                                      border: Border.all(color: Colors.grey.shade300, width: 1),
                                    ),
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        Row(
                                          children: [
                                            Icon(Icons.calendar_month_outlined, color: Colors.grey.shade600),
                                            const SizedBox(width: 12),
                                            Text(
                                              _selectedDateOfBirth != null
                                                  ? "${_selectedDateOfBirth!.day}/${_selectedDateOfBirth!.month}/${_selectedDateOfBirth!.year}"
                                                  : 'Chọn ngày sinh',
                                              style: TextStyle(
                                                fontSize: 15,
                                                color: _selectedDateOfBirth != null ? Colors.black87 : Colors.grey.shade400,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const Icon(Icons.arrow_drop_down_rounded),
                                      ],
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 16),

                                // Gender Input (Dropdown)
                                DropdownButtonFormField<String>(
                                  value: _selectedGender,
                                  decoration: const InputDecoration(
                                    labelText: 'Giới tính',
                                    prefixIcon: Icon(Icons.transgender_rounded),
                                  ),
                                  items: _genders.map((gender) {
                                    String text = 'Khác';
                                    if (gender == 'male') text = 'Nam';
                                    if (gender == 'female') text = 'Nữ';
                                    return DropdownMenuItem(
                                      value: gender,
                                      child: Text(text),
                                    );
                                  }).toList(),
                                  onChanged: (val) {
                                    setState(() {
                                      _selectedGender = val;
                                    });
                                  },
                                ),
                                const SizedBox(height: 16),

                                // Blood Type Input (Dropdown)
                                DropdownButtonFormField<String>(
                                  value: _selectedBloodType,
                                  decoration: const InputDecoration(
                                    labelText: 'Nhóm máu',
                                    prefixIcon: Icon(Icons.bloodtype_outlined),
                                  ),
                                  items: _bloodTypes.map((blood) {
                                    return DropdownMenuItem(
                                      value: blood,
                                      child: Text(blood),
                                    );
                                  }).toList(),
                                  onChanged: (val) {
                                    setState(() {
                                      _selectedBloodType = val;
                                    });
                                  },
                                ),
                                const SizedBox(height: 16),

                                // Address Input
                                TextFormField(
                                  controller: _addressController,
                                  decoration: const InputDecoration(
                                    labelText: 'Địa chỉ thường trú',
                                    prefixIcon: Icon(Icons.home_outlined),
                                  ),
                                ),
                                const SizedBox(height: 16),

                                // Emergency Contact Input
                                TextFormField(
                                  controller: _emergencyController,
                                  decoration: const InputDecoration(
                                    labelText: 'Liên hệ khẩn cấp',
                                    prefixIcon: Icon(Icons.contact_phone_outlined),
                                    hintText: 'Tên - SĐT',
                                  ),
                                ),
                                const SizedBox(height: 16),

                                // Insurance Number Input
                                TextFormField(
                                  controller: _insuranceController,
                                  decoration: const InputDecoration(
                                    labelText: 'Số thẻ BHYT',
                                    prefixIcon: Icon(Icons.credit_card_outlined),
                                  ),
                                ),
                                const SizedBox(height: 24),

                                // Save Button
                                profileNotifier.isLoading
                                    ? const Center(child: CircularProgressIndicator())
                                    : ElevatedButton(
                                        onPressed: _saveProfile,
                                        child: const Text('LƯU THAY ĐỔI'),
                                      ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildReadOnlyRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontSize: 13, color: Colors.black54)),
          Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87)),
        ],
      ),
    );
  }
}
