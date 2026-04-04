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
            'recentAppointments' => array_slice($appointmentModel->getAll(), 0, 5),
        ];

        // Lấy dữ liệu theo role
        if ($role === 'doctor') {
            $doctor = $doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $data['doctorInfo'] = $doctor;
                $data['myAppointments'] = $appointmentModel->getByDoctorId($doctor['id']);
            }
        }

        if ($role === 'patient') {
            $patient = $patientModel->findByUserId($user['id']);
            if ($patient) {
                $data['patientInfo'] = $patient;
                $data['myAppointments'] = $appointmentModel->getByPatientId($patient['id']);
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
