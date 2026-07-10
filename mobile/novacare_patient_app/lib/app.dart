import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/theme/app_theme.dart';
import 'core/network/api_client.dart';
import 'routes/app_routes.dart';

// Notifiers imports
import 'features/auth/auth_notifier.dart';
import 'features/home/home_notifier.dart';
import 'features/profile/profile_notifier.dart';
import 'features/departments/departments_notifier.dart';
import 'features/doctors/doctors_notifier.dart';
import 'features/appointments/appointment_notifier.dart';
import 'features/queue/queue_notifier.dart';
import 'features/records/record_notifier.dart';
import 'features/prescriptions/prescription_notifier.dart';
import 'features/invoices/invoice_notifier.dart';
import 'features/ai_chat/ai_chat_notifier.dart';
import 'features/notifications/notification_notifier.dart';

class NovaCareApp extends StatelessWidget {
  final ApiClient apiClient;

  const NovaCareApp({super.key, required this.apiClient});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => HomeNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => ProfileNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => DepartmentsNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => DoctorsNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => AppointmentNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => QueueNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => RecordNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => PrescriptionNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => InvoiceNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => AIChatNotifier(apiClient)),
        ChangeNotifierProvider(create: (_) => NotificationNotifier(apiClient)),
      ],
      child: MaterialApp(
        title: 'NovaCare Patient',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        navigatorKey: ApiClient.navigatorKey, // Dùng để logout và điều hướng tự động khi token hết hạn
        initialRoute: AppRoutes.splash,
        routes: AppRoutes.routes,
      ),
    );
  }
}
