import 'package:flutter/material.dart';
import '../features/auth/login_screen.dart';
import '../features/auth/register_screen.dart';
import '../features/auth/splash_screen.dart';
import '../features/home/home_screen.dart';
import '../features/profile/profile_screen.dart';
import '../features/departments/departments_screen.dart';
import '../features/doctors/doctors_screen.dart';
import '../features/appointments/book_appointment_screen.dart';
import '../features/appointments/my_appointments_screen.dart';
import '../features/appointments/appointment_detail_screen.dart';
import '../features/queue/queue_tracking_screen.dart';
import '../features/records/medical_records_screen.dart';
import '../features/records/record_detail_screen.dart';
import '../features/prescriptions/prescriptions_screen.dart';
import '../features/prescriptions/prescription_detail_screen.dart';
import '../features/invoices/invoices_screen.dart';
import '../features/invoices/invoice_detail_screen.dart';
import '../features/ai_chat/ai_chat_screen.dart';
import '../features/notifications/notifications_screen.dart';
import '../features/settings/settings_screen.dart';

class AppRoutes {
  static const String splash = '/';
  static const String login = '/login';
  static const String register = '/register';
  static const String home = '/home';
  static const String profile = '/profile';
  static const String departments = '/departments';
  static const String doctors = '/doctors';
  static const String bookAppointment = '/book-appointment';
  static const String myAppointments = '/my-appointments';
  static const String appointmentDetail = '/appointment-detail';
  static const String queue = '/queue';
  static const String records = '/records';
  static const String recordDetail = '/record-detail';
  static const String prescriptions = '/prescriptions';
  static const String prescriptionDetail = '/prescription-detail';
  static const String invoices = '/invoices';
  static const String invoiceDetail = '/invoice-detail';
  static const String aiChat = '/ai-chat';
  static const String notifications = '/notifications';
  static const String settings = '/settings';

  static Map<String, WidgetBuilder> get routes => {
        splash: (context) => const SplashScreen(),
        login: (context) => const LoginScreen(),
        register: (context) => const RegisterScreen(),
        home: (context) => const HomeScreen(),
        profile: (context) => const ProfileScreen(),
        departments: (context) => const DepartmentsScreen(),
        doctors: (context) => const DoctorsScreen(),
        bookAppointment: (context) => const BookAppointmentScreen(),
        myAppointments: (context) => const MyAppointmentsScreen(),
        appointmentDetail: (context) {
          final id = ModalRoute.of(context)!.settings.arguments as int;
          return AppointmentDetailScreen(appointmentId: id);
        },
        queue: (context) => const QueueTrackingScreen(),
        records: (context) => const MedicalRecordsScreen(),
        recordDetail: (context) {
          final id = ModalRoute.of(context)!.settings.arguments as int;
          return RecordDetailScreen(recordId: id);
        },
        prescriptions: (context) => const PrescriptionsScreen(),
        prescriptionDetail: (context) {
          final id = ModalRoute.of(context)!.settings.arguments as int;
          return PrescriptionDetailScreen(prescriptionId: id);
        },
        invoices: (context) => const InvoicesScreen(),
        invoiceDetail: (context) {
          final id = ModalRoute.of(context)!.settings.arguments as int;
          return InvoiceDetailScreen(invoiceId: id);
        },
        aiChat: (context) => const AIChatScreen(),
        notifications: (context) => const NotificationsScreen(),
        settings: (context) => const SettingsScreen(),
      };
}
