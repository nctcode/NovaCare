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
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Shift.php';

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

        // Dữ liệu thực tế từ cơ sở dữ liệu cho dashboard
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmtDep = $conn->query("SELECT COUNT(*) as total FROM departments");
        $totalDeps = $stmtDep->fetch()['total'];

        $stmtDev = $conn->query("SELECT COUNT(*) as total FROM medical_devices");
        $totalDevs = $stmtDev->fetch()['total'];

        $stmtDevAvail = $conn->query("SELECT COUNT(*) as total FROM medical_devices WHERE status = 'available'");
        $availDevs = $stmtDevAvail->fetch()['total'];

        require_once __DIR__ . '/../models/Admission.php';
        $admissionModel = new Admission();
        $activeInpatients = $admissionModel->countActive();

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
            'totalDepartments' => $totalDeps,
            'totalDevices'     => $totalDevs,
            'availableDevices' => $availDevs,
            'totalInpatients'  => $activeInpatients,
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
                $shiftModel = new Shift();
                $data['myShifts'] = $shiftModel->getShiftsByDoctorId($doctor['id']);
                // Lấy tư vấn online
                require_once __DIR__ . '/../models/OnlineConsultation.php';
                $consultModel = new OnlineConsultation();
                $data['myConsultations'] = $consultModel->getByDoctorId($doctor['id']);

                // Lớp 1 & Lớp 4: Kiểm tra quota ca đêm tuần này
                $weekStart = date('Y-m-d', strtotime('monday this week'));
                $nightCount = $shiftModel->countNightShiftsInWeek($doctor['id'], $weekStart);
                $data['nightShiftsThisWeek'] = $nightCount;

                // Gửi thông báo tự động (Lớp 4) nếu thiếu quota và chưa gửi tuần này
                if ($nightCount < 2) {
                    $notifModel = new Notification();
                    if (!$notifModel->hasShiftReminderThisWeek($user['id'])) {
                        $notifModel->create(
                            $user['id'],
                            '🌙 Nhắc nhở ca trực đêm',
                            'Lưu ý: Bạn mới chỉ đăng ký ' . $nightCount . '/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Trưởng khoa chỉ định trực.'
                        );
                    }
                }

                // Lớp 3: Nếu là Trưởng khoa, lấy thống kê quota của khoa
                require_once __DIR__ . '/../helpers/Security.php';
                $isHead = Security::isHeadOfDepartment() !== false;
                if ($isHead) {
                    $data['isHead'] = true;
                    $data['shiftQuotaStats'] = $shiftModel->getQuotaStats($doctor['department_id']);
                }
            }
        }

        if ($role === 'nurse') {
            $nurse = $nurseModel->findByUserId($user['id']);
            if ($nurse) {
                $data['nurseInfo'] = $nurse;
                // Ca trực của y tá
                $shiftModel = new Shift();
                $data['myShifts'] = $shiftModel->getShiftsByNurseId($nurse['id']);
                // Bệnh nhân nội trú (y tá cần xem)
                require_once __DIR__ . '/../models/Admission.php';
                $admissionModel = new Admission();
                $data['activeAdmissions'] = $admissionModel->countActive();

                // Lớp 1 & Lớp 4: Kiểm tra quota ca đêm tuần này
                $weekStart = date('Y-m-d', strtotime('monday this week'));
                $nightCount = $shiftModel->countNurseNightShiftsInWeek($nurse['id'], $weekStart);
                $data['nightShiftsThisWeek'] = $nightCount;

                // Gửi thông báo tự động (Lớp 4) nếu thiếu quota và chưa gửi tuần này
                if ($nightCount < 2) {
                    $notifModel = new Notification();
                    if (!$notifModel->hasShiftReminderThisWeek($user['id'])) {
                        $notifModel->create(
                            $user['id'],
                            '🌙 Nhắc nhở ca trực đêm',
                            'Lưu ý: Bạn mới chỉ đăng ký ' . $nightCount . '/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Điều dưỡng trưởng chỉ định trực.'
                        );
                    }
                }

                // Lớp 3: Nếu là Điều dưỡng trưởng (Head Nurse), lấy thống kê quota của khoa
                require_once __DIR__ . '/../helpers/Security.php';
                $isHead = Security::isHeadOfDepartment() !== false;
                if ($isHead) {
                    $data['isHead'] = true;
                    $data['shiftQuotaStats'] = $shiftModel->getQuotaStats($nurse['department_id']);
                }
            }
        }

        if ($role === 'receptionist') {
            // Lịch hẹn hôm nay
            $allAppointments = $appointmentModel->getAll();
            $data['todayAppointments'] = array_filter($allAppointments, function($a) {
                return date('Y-m-d', strtotime($a['appointment_date'])) === date('Y-m-d');
            });
            $data['todayAppointmentsCount'] = count($data['todayAppointments']);
            $data['recentAppointments'] = $appointmentModel->getRecentAppointments(5);
            // Nội trú
            require_once __DIR__ . '/../models/Admission.php';
            $admissionModel = new Admission();
            $data['activeAdmissions'] = $admissionModel->countActive();
            // Hàng chờ hôm nay
            require_once __DIR__ . '/../models/QueueTicket.php';
            $queueModel = new QueueTicket();
            $data['queueStats'] = $queueModel->getStatsToday();
            $data['waitingTickets'] = $queueModel->getNextWaiting(5);
        }

        if ($role === 'cashier') {
            require_once __DIR__ . '/../models/Invoice.php';
            $invoiceModel = new Invoice();
            $data['pendingInvoicesCount'] = $invoiceModel->countByStatus('pending');
            $data['totalRevenue'] = $invoiceModel->getTotalRevenue();
            $data['recentInvoices'] = $invoiceModel->getAll();
        }

        if ($role === 'pharmacist') {
            // Thuốc sắp hết
            $lowStockMedicines = $medicineModel->getLowStock();
            $data['lowStockMedicines'] = $lowStockMedicines;
            $data['totalLowStock'] = count($lowStockMedicines);
            // Đơn thuốc gần đây
            require_once __DIR__ . '/../models/Prescription.php';
            $prescriptionModel = new Prescription();
            $data['recentPrescriptions'] = $prescriptionModel->getAll();
            $data['totalPrescriptions'] = count($data['recentPrescriptions']);
            // Thuốc sắp hết hạn
            $data['expiringMedicines'] = $medicineModel->getExpiringSoon();
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

        // Technician dashboard data
        if ($role === 'technician') {
            try {
                require_once __DIR__ . '/../models/LabOrder.php';
                $labModel = new LabOrder();
                $pendingLabOrders = $labModel->countByStatus('pending');
                $inProgressLabOrders = $labModel->countByStatus('in_progress');
                $completedLabOrders = $labModel->countByStatus('completed');
                $totalLabOrders = $labModel->count();
                $pendingOrders = $labModel->getPending();
            } catch (Exception $e) {
                $pendingLabOrders = $inProgressLabOrders = $completedLabOrders = $totalLabOrders = 0;
                $pendingOrders = [];
            }
        }

        // Director dashboard data
        if ($role === 'director') {
            require_once __DIR__ . '/../controllers/ReportController.php';
            $reportCtrl = new ReportController();
            
            $directorStats = $reportCtrl->getOverviewStats();
            $revenueByMonth = $reportCtrl->getRevenueByMonth();
            $appointmentsByStatus = $reportCtrl->getAppointmentsByStatus();
            $topDoctors = $reportCtrl->getTopDoctors();
            $bedOccupancy = $reportCtrl->getBedOccupancy();
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
            case 'receptionist':
                require_once __DIR__ . '/../views/dashboard/receptionist.php';
                break;
            case 'pharmacist':
                require_once __DIR__ . '/../views/dashboard/pharmacist.php';
                break;
            case 'technician':
                require_once __DIR__ . '/../views/dashboard/technician.php';
                break;
            case 'director':
                require_once __DIR__ . '/../views/dashboard/director.php';
                break;
            case 'cashier':
                require_once __DIR__ . '/../views/dashboard/cashier.php';
                break;
        }

        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * AI Dự đoán Lưu lượng Bệnh nhân & Tải Bệnh viện (AJAX API) - Smart Hospital 4.0
     * 
     * Phân tích lịch hẹn 30 ngày qua + công suất giường bệnh + khoa/phòng để dự báo tải vận hành
     */
    public function aiPredictLoad() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['director', 'admin']);

        $db = new Database();
        $conn = $db->getConnection();

        // 1. Thu thập data quá khứ (Lịch hẹn 30 ngày qua theo DOW)
        $sqlAppts = "SELECT DAYOFWEEK(appointment_date) as dow, COUNT(*) as count 
                     FROM appointments 
                     WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                     GROUP BY DAYOFWEEK(appointment_date)";
        $stmt = $conn->query($sqlAppts);
        $apptHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dowNames = [1 => 'Chủ Nhật', 2 => 'Thứ Hai', 3 => 'Thứ Ba', 4 => 'Thứ Tư', 5 => 'Thứ Năm', 6 => 'Thứ Sáu', 7 => 'Thứ Bảy'];
        $historyText = "LƯỢT KHÁM HẰNG NGÀY TRONG 30 NGÀY QUA:\n";
        foreach ($apptHistory as $h) {
            $historyText .= "- " . ($dowNames[$h['dow']] ?? 'N/A') . ": " . $h['count'] . " lượt khám\n";
        }

        // 2. Dữ liệu công suất hiện tại
        $stmtBeds = $conn->query("SELECT COUNT(*) as total FROM beds");
        $totalBeds = $stmtBeds->fetch()['total'] ?? 0;
        
        $stmtOcc = $conn->query("SELECT COUNT(*) as occupied FROM beds WHERE status = 'occupied'");
        $occupiedBeds = $stmtOcc->fetch()['occupied'] ?? 0;

        $stmtInpatients = $conn->query("SELECT COUNT(*) as total FROM admissions WHERE status = 'active' AND deleted_at IS NULL");
        $currentInpatients = $stmtInpatients->fetch()['total'] ?? 0;

        // 3. Số ca phân bổ theo khoa khám bệnh (qua số medical records)
        $sqlDepts = "SELECT dep.name as dept_name, COUNT(mr.id) as count
                     FROM medical_records mr
                     JOIN doctors d ON mr.doctor_id = d.id
                     JOIN departments dep ON d.department_id = dep.id
                     WHERE mr.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                     GROUP BY dep.id
                     ORDER BY count DESC";
        $stmtDepts = $conn->query($sqlDepts);
        $deptHistory = $stmtDepts->fetchAll(PDO::FETCH_ASSOC);

        $deptText = "PHÂN BỔ BỆNH NHÂN THEO KHOA (30 ngày):\n";
        foreach ($deptHistory as $d) {
            $deptText .= "- Khoa " . $d['dept_name'] . ": " . $d['count'] . " bệnh nhân\n";
        }

        // 4. Tạo prompt AI dự báo tải
        $prompt = "Bạn là trợ lý AI phân tích và dự báo tải vận hành y tế của Bệnh viện NovaCare 4.0.\n\n"
            . "DỮ LIỆU HIỆN TẠI VÀ QUÁ KHỨ BỆNH VIỆN:\n"
            . "- Tổng số giường bệnh nội trú: " . $totalBeds . "\n"
            . "- Số giường đang sử dụng: " . $occupiedBeds . " (" . ($totalBeds > 0 ? round(($occupiedBeds/$totalBeds)*100, 1) : 0) . "%)\n"
            . "- Bệnh nhân điều trị nội trú hiện tại: " . $currentInpatients . "\n\n"
            . $historyText . "\n"
            . $deptText . "\n"
            . "YÊU CẦU: Hãy phân tích xu hướng lưu lượng bệnh nhân và dự báo 7 ngày tới.\n"
            . "BẮT BUỘC trả về duy nhất định dạng JSON sau:\n"
            . "{\n"
            . "  \"predicted_load_percentage\": 75,\n"
            . "  \"risk_level\": \"normal / warning / danger / critical\",\n"
            . "  \"peak_days\": [\"Thứ Hai\", \"Thứ Sáu\"],\n"
            . "  \"busiest_department\": \"Tên khoa bận rộn nhất\",\n"
            . "  \"forecast_7days\": [\n"
            . "     {\"day\": \"Thứ Hai\", \"estimated_patients\": 45, \"load_status\": \"high\"},\n"
            . "     {\"day\": \"Thứ Ba\", \"estimated_patients\": 30, \"load_status\": \"medium\"},\n"
            . "     {\"day\": \"Thứ Tư\", \"estimated_patients\": 25, \"load_status\": \"low\"},\n"
            . "     {\"day\": \"Thứ Năm\", \"estimated_patients\": 28, \"load_status\": \"medium\"},\n"
            . "     {\"day\": \"Thứ Sáu\", \"estimated_patients\": 42, \"load_status\": \"high\"},\n"
            . "     {\"day\": \"Thứ Bảy\", \"estimated_patients\": 20, \"load_status\": \"low\"},\n"
            . "     {\"day\": \"Chủ Nhật\", \"estimated_patients\": 15, \"load_status\": \"low\"}\n"
            . "  ],\n"
            . "  \"analysis\": \"Phân tích chi tiết về xu hướng tăng/giảm bệnh nhân trong tuần tới\",\n"
            . "  \"recommendations\": \"Đề xuất phân bổ ca trực cho bác sĩ/y tá, mua thêm vật tư/thuốc, chuẩn bị giường trống...\"\n"
            . "}";

        require_once __DIR__ . '/../models/BeeknoeeAI.php';
        $ai = new BeeknoeeAI();
        if (!$ai->isConfigured()) {
            require_once __DIR__ . '/../models/GeminiAI.php';
            $ai = new GeminiAI();
            if (!$ai->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'AI chưa được cấu hình.']);
                exit;
            }
        }

        $ai->systemPrompt = "Bạn là AI dự báo lưu lượng bệnh viện thông minh. Chỉ trả về JSON.";
        $result = $ai->chat($prompt);

        if ($result['success']) {
            $parsed = $result['data'];
            if (isset($parsed['predicted_load_percentage'])) {
                echo json_encode(['success' => true, 'data' => $parsed]);
            } else {
                $rawText = $result['raw'] ?? '';
                if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                    $jsonDecoded = json_decode($matches[0], true);
                    if ($jsonDecoded && isset($jsonDecoded['predicted_load_percentage'])) {
                        echo json_encode(['success' => true, 'data' => $jsonDecoded]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'error' => 'AI phản hồi sai định dạng.', 'raw' => $rawText]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Không thể liên kết với AI.']);
        }
        exit;
    }
}