<?php
/**
 * Routing - Định nghĩa các route cho hệ thống
 * 
 * Mỗi route map "page" → controller class
 * Action mặc định là "index"
 */

$routes = [
    'login'         => 'AuthController',
    'logout'        => 'AuthController',
    'dashboard'     => 'DashboardController',
    'patients'      => 'PatientController',
    'doctors'       => 'DoctorController',
    'appointments'  => 'AppointmentController',
    'prescriptions' => 'PrescriptionController',
    'medicines'     => 'MedicineController',
    'shifts'        => 'ShiftController',
    'devices'       => 'DeviceController',
    'nurses'        => 'NurseController',
    'technicians'   => 'TechnicianController',
    'receptionists' => 'ReceptionistController',
    'pharmacists'   => 'PharmacistController',
    'cashiers'      => 'CashierController',
    'directors'     => 'DirectorController',
    'departments'   => 'DepartmentController',
    'services-admin'=> 'ServiceController',
    'consultations' => 'ConsultationController',
    'ai-assistant'  => 'AIAssistantController',
    'equipment'     => 'EquipmentController',
    'records'       => 'MedicalRecordController',
    'invoices'      => 'InvoiceController',
    'inpatient'     => 'InpatientController',
    'audit_logs'    => 'AuditLogController',
    'users'         => 'UserController',
    'ai-admin'      => 'AdminAIController',
    'lab-orders'    => 'LabOrderController',
    'reports'       => 'ReportController',
    'queue'         => 'QueueController',
];
