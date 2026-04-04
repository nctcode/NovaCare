<?php
/**
 * ConsultationController - Tư vấn trực tuyến (Online Meetings)
 * Replaces old MeetingController with improved functionality
 */
require_once __DIR__ . '/../models/OnlineConsultation.php';

class ConsultationController {
    private $model;

    public function __construct() {
        $this->model = new OnlineConsultation();
    }

    public function index() {
        $user = $_SESSION['user'];

        if ($user['role'] === 'admin') {
            $meetings = $this->model->getAll();
            $availableAppointments = $this->model->getAvailableAppointments();
        } elseif ($user['role'] === 'doctor') {
            $meetings = $this->model->getByDoctorUserId($user['id']);
            $availableAppointments = [];
        } elseif ($user['role'] === 'patient') {
            $meetings = $this->model->getByPatientUserId($user['id']);
            $availableAppointments = [];
        } else {
            $meetings = [];
            $availableAppointments = [];
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/consultations/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user']['role'] === 'admin') {
            $appointmentId = $_POST['appointment_id'];
            $result = $this->model->create($appointmentId);
            
            if ($result) {
                // Simulate email reminder
                $_SESSION['success'] = '✅ Phòng tư vấn đã tạo thành công!<br>
                    📧 Email reminder sent to patient with meeting link: <strong>' . $result['meeting_link'] . '</strong><br>
                    🔗 Meeting ID: <strong>' . $result['meeting_id'] . '</strong>';
            } else {
                $_SESSION['error'] = 'Không thể tạo phòng tư vấn.';
            }
        }
        header('Location: index.php?page=consultations');
        exit;
    }
}
