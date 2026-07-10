<?php
/**
 * API Routing - Bản đồ định tuyến cho REST API v1
 * Lưu ý: Các route tĩnh phải đứng trước route động {id} hoặc {invoice_id} để tránh bị nhận diện nhầm.
 */
return [
    'POST' => [
        // Auth endpoints
        'api/v1/auth/login'              => ['controller' => 'ApiAuthController', 'action' => 'login', 'middleware' => []],
        'api/v1/auth/register'           => ['controller' => 'ApiAuthController', 'action' => 'register', 'middleware' => []],
        'api/v1/auth/refresh'            => ['controller' => 'ApiAuthController', 'action' => 'refresh', 'middleware' => []],
        'api/v1/auth/logout'             => ['controller' => 'ApiAuthController', 'action' => 'logout', 'middleware' => []],
        
        // Payments endpoints (Static first)
        'api/v1/payments/vnpay/create'   => ['controller' => 'ApiPaymentController', 'action' => 'createVNPay', 'middleware' => ['auth', 'role:patient']],
        'api/v1/payments/momo/create'    => ['controller' => 'ApiPaymentController', 'action' => 'createMoMo', 'middleware' => ['auth', 'role:patient']],
        'api/v1/payments/vnpay/ipn'      => ['controller' => 'ApiPaymentController', 'action' => 'vnpayIpn', 'middleware' => []],

        // AI Chat endpoints (Static first)
        'api/v1/ai/chat'                 => ['controller' => 'ApiAIController', 'action' => 'chat', 'middleware' => ['auth', 'role:patient']],

        // Notifications endpoints (Static first)
        'api/v1/notifications/read-all'  => ['controller' => 'ApiNotificationController', 'action' => 'readAll', 'middleware' => ['auth', 'role:patient']],
        'api/v1/notifications/{id}/read' => ['controller' => 'ApiNotificationController', 'action' => 'read', 'middleware' => ['auth', 'role:patient']],

        // Appointment actions (Specific static actions must go first, cancel is dynamic)
        'api/v1/appointments/{id}/cancel' => ['controller' => 'ApiAppointmentController', 'action' => 'cancel', 'middleware' => ['auth', 'role:patient']],
        'api/v1/appointments'            => ['controller' => 'ApiAppointmentController', 'action' => 'create', 'middleware' => ['auth', 'role:patient']],
    ],
    'GET' => [
        // Auth & Patient Profile
        'api/v1/me'                      => ['controller' => 'ApiAuthController', 'action' => 'me', 'middleware' => ['auth']],
        'api/v1/patient/profile'         => ['controller' => 'ApiPatientController', 'action' => 'getProfile', 'middleware' => ['auth', 'role:patient']],

        // Departments & Doctors (Public)
        'api/v1/departments'             => ['controller' => 'ApiDepartmentController', 'action' => 'index', 'middleware' => []],
        'api/v1/doctors'                 => ['controller' => 'ApiDoctorController', 'action' => 'index', 'middleware' => []],

        // Payments (Static first)
        'api/v1/payments/history'        => ['controller' => 'ApiPaymentController', 'action' => 'history', 'middleware' => ['auth', 'role:patient']],
        'api/v1/payments/vnpay/return'   => ['controller' => 'ApiPaymentController', 'action' => 'vnpayReturn', 'middleware' => []],
        'api/v1/payments/{invoice_id}/status' => ['controller' => 'ApiPaymentController', 'action' => 'status', 'middleware' => ['auth', 'role:patient']],

        // Appointments (Static endpoints must go before dynamic {id})
        'api/v1/appointments/available-slots' => ['controller' => 'ApiAppointmentController', 'action' => 'availableSlots', 'middleware' => ['auth', 'role:patient']],
        'api/v1/appointments/my'              => ['controller' => 'ApiAppointmentController', 'action' => 'getMyAppointments', 'middleware' => ['auth', 'role:patient']],
        'api/v1/appointments/{id}'            => ['controller' => 'ApiAppointmentController', 'action' => 'show', 'middleware' => ['auth', 'role:patient']],

        // AI Chat (Static first)
        'api/v1/ai/chat-history'         => ['controller' => 'ApiAIController', 'action' => 'chatHistory', 'middleware' => ['auth', 'role:patient']],

        // Queue
        'api/v1/queue/my-ticket'         => ['controller' => 'ApiQueueController', 'action' => 'getMyTicket', 'middleware' => ['auth', 'role:patient']],

        // Medical Records (Static first)
        'api/v1/records/my'              => ['controller' => 'ApiMedicalRecordController', 'action' => 'index', 'middleware' => ['auth', 'role:patient']],
        'api/v1/records/{id}'            => ['controller' => 'ApiMedicalRecordController', 'action' => 'show', 'middleware' => ['auth', 'role:patient']],

        // Prescriptions (Static first)
        'api/v1/prescriptions/my'        => ['controller' => 'ApiPrescriptionController', 'action' => 'index', 'middleware' => ['auth', 'role:patient']],
        'api/v1/prescriptions/{id}'      => ['controller' => 'ApiPrescriptionController', 'action' => 'show', 'middleware' => ['auth', 'role:patient']],

        // Notifications (Static first)
        'api/v1/notifications/unread-count' => ['controller' => 'ApiNotificationController', 'action' => 'unreadCount', 'middleware' => ['auth', 'role:patient']],
        'api/v1/notifications'             => ['controller' => 'ApiNotificationController', 'action' => 'index', 'middleware' => ['auth', 'role:patient']],

        // Invoices (Static first)
        'api/v1/invoices/my'             => ['controller' => 'ApiInvoiceController', 'action' => 'index', 'middleware' => ['auth', 'role:patient']],
        'api/v1/invoices/{id}'           => ['controller' => 'ApiInvoiceController', 'action' => 'show', 'middleware' => ['auth', 'role:patient']],
    ],
    'PUT' => [
        'api/v1/patient/profile'         => ['controller' => 'ApiPatientController', 'action' => 'updateProfile', 'middleware' => ['auth', 'role:patient']],
    ],
    'DELETE' => [
        // AI Chat (Static first)
        'api/v1/ai/chat-history'         => ['controller' => 'ApiAIController', 'action' => 'deleteChatHistory', 'middleware' => ['auth', 'role:patient']],
    ]
];
