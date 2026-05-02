<?php
/**
 * DashboardController - Hiển thị dashboard theo role
 */
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Nurse.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Medicine.php';
require_once __DIR__ . '/../models/Equipment.php';

class DashboardController {

    public function index() {
        $user = $_SESSION['user'];
        $role = $user['role'];

        $patientModel = new Patient();
        $doctorModel = new Doctor();
        $nurseModel = new Nurse();
        $appointmentModel = new Appointment();
        $medicineModel = new Medicine();
        $equipmentModel = new Equipment();

        // Dữ liệu cho dashboard
        $data = [
            'totalPatients'    => $patientModel->count(),
            'totalDoctors'     => $doctorModel->count(),
            'totalNurses'      => $nurseModel->count(),
            'totalAppointments'=> $appointmentModel->count(),
            'pendingAppointments' => $appointmentModel->countPending(),
            'confirmedAppointments' => $appointmentModel->countByStatus('confirmed'),
            'completedAppointments' => $appointmentModel->countByStatus('completed'),
            'cancelledAppointments' => $appointmentModel->countByStatus('cancelled'),
            'totalMedicines'   => $medicineModel->count(),
            'totalEquipment'   => $equipmentModel->count(),
        ];

        // Lấy 5 bệnh nhân mới nhất
        $recentPatients = [];
        $lowStockMedicines = [];
        if ($role === 'admin') {
            $recentPatients = $patientModel->getRecentPatients(5);
            $recentAppointments = $appointmentModel->getRecentAppointments(5);
            $lowStockMedicines = $medicineModel->getLowStock();
            
            // Limit to 5 for UI consistency
            if (count($lowStockMedicines) > 5) {
                $lowStockMedicines = array_slice($lowStockMedicines, 0, 5);
            }
        }

        // Lấy dữ liệu theo role
        if ($role === 'doctor') {
            $doctor = $doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $data['doctorInfo'] = $doctor;
                $data['myAppointments'] = $appointmentModel->getByDoctorId($doctor['id']);
                // Lấy ca trực
                require_once __DIR__ . '/../models/Shift.php';
                $shiftModel = new Shift();
                $data['myShifts'] = $shiftModel->getShiftsByDoctorId($doctor['id']);
                // Lấy tư vấn online
                require_once __DIR__ . '/../models/OnlineConsultation.php';
                $consultModel = new OnlineConsultation();
                $data['myConsultations'] = $consultModel->getByDoctorId($doctor['id']);
            }
        }

        if ($role === 'patient') {
            $patient = $patientModel->findByUserId($user['id']);
            if ($patient) {
                $data['patientInfo'] = $patient;
                
                // Tổng số (cho widget)
                $allAppointments = $appointmentModel->getByPatientId($patient['id']);
                $data['totalAppointments'] = count($allAppointments);
                $data['myAppointments'] = $appointmentModel->getRecentByPatientId($patient['id'], 4);
                
                // Lấy tư vấn online
                require_once __DIR__ . '/../models/OnlineConsultation.php';
                $consultModel = new OnlineConsultation();
                $allConsultations = $consultModel->getByPatientId($patient['id']);
                $data['totalConsultations'] = count($allConsultations);
                $data['myConsultations'] = $consultModel->getRecentByPatientId($patient['id'], 2);
                
                // Lấy hồ sơ bệnh án
                require_once __DIR__ . '/../models/MedicalRecord.php';
                $recordModel = new MedicalRecord();
                $allRecords = $recordModel->getByPatientId($patient['id']);
                $data['totalRecords'] = count($allRecords);
                $data['myMedicalRecords'] = $recordModel->getRecentByPatientId($patient['id'], 3);

                // Lấy hóa đơn chưa thanh toán
                require_once __DIR__ . '/../models/Invoice.php';
                $invoiceModel = new Invoice();
                $data['pendingInvoices'] = $invoiceModel->getPendingByPatientId($patient['id']);

                // Lấy đơn thuốc mới nhất
                require_once __DIR__ . '/../models/Prescription.php';
                $prescriptionModel = new Prescription();
                $prescriptions = $prescriptionModel->getByPatientId($patient['id']);
                $data['latestPrescription'] = !empty($prescriptions) ? $prescriptions[0] : null;
            }
        }

        $pageTitle = 'Dashboard';
        require_once __DIR__ . '/../views/layout/header.php';

        // Load view theo role
        switch ($role) {
            case 'admin':
                require_once __DIR__ . '/../views/dashboard/admin.php';
                break;
            case 'doctor':
                require_once __DIR__ . '/../views/dashboard/doctor.php';
                break;
            case 'nurse':
                require_once __DIR__ . '/../views/dashboard/nurse.php';
                break;
            case 'patient':
                require_once __DIR__ . '/../views/dashboard/patient.php';
                break;
        }

        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
