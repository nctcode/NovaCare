class ApiConstants {
  static const String baseUrl = 'http://10.0.2.2/NovaCare/api/v1';

  // Auth endpoints
  static const String register = '/auth/register';
  static const String login = '/auth/login';
  static const String refresh = '/auth/refresh';
  static const String logout = '/auth/logout';
  static const String me = '/me';

  // Patient profile
  static const String profile = '/patient/profile';

  // Departments & Doctors
  static const String departments = '/departments';
  static const String doctors = '/doctors';

  // Appointments
  static const String availableSlots = '/appointments/available-slots';
  static const String appointments = '/appointments';
  static const String myAppointments = '/appointments/my';

  // Queue
  static const String myTicket = '/queue/my-ticket';

  // Medical Records & Prescriptions
  static const String medicalRecords = '/records/my';
  static const String prescriptions = '/prescriptions/my';

  // Invoices & Payments
  static const String invoices = '/invoices/my';
  static const String vnpayCreate = '/payments/vnpay/create';
  static const String momoCreate = '/payments/momo/create';
  static const String paymentHistory = '/payments/history';

  // AI Chat
  static const String aiChat = '/ai/chat';
  static const String aiChatHistory = '/ai/chat-history';

  // Notifications
  static const String notifications = '/notifications';
  static const String unreadCount = '/notifications/unread-count';
}
