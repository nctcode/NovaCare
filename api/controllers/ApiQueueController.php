<?php
/**
 * ApiQueueController - Phục vụ dữ liệu Hàng chờ khám (Queue Ticket) của Bệnh nhân
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/QueueTicket.php';

class ApiQueueController extends BaseApiController {

    private $patientModel;
    private $queueModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->queueModel = new QueueTicket();
    }

    /**
     * GET /api/v1/queue/my-ticket
     */
    public function getMyTicket() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy thông tin hồ sơ bệnh nhân.');
        }

        // Lấy số thứ tự khám hôm nay chưa hoàn thành/hủy
        $ticket = $this->queueModel->getByPatientId($patient['id']);

        if (!$ticket) {
            // Trường hợp không có ticket nào hôm nay, trả về 200 OK với data: null
            return $this->sendSuccess(
                null, 
                'Bệnh nhân chưa có số thứ tự khám hôm nay'
            );
        }

        $db = new Database();
        $conn = $db->getConnection();

        // Lấy số thứ tự đang được gọi của phòng khám/khoa liên quan
        $currentCalling = 0;
        if (!empty($ticket['department_id'])) {
            $sqlCalling = "SELECT MAX(ticket_number) as max_num FROM queue_tickets 
                           WHERE department_id = :dept_id 
                             AND status IN ('called', 'in_progress') 
                             AND queue_date = CURDATE()";
            $stmt = $conn->prepare($sqlCalling);
            $stmt->execute([':dept_id' => $ticket['department_id']]);
            $res = $stmt->fetch();
            $currentCalling = (int)($res['max_num'] ?? 0);
        }

        // Số người đang chờ trước bệnh nhân này
        $estimatedWaitingCount = $this->queueModel->countAhead($ticket['ticket_number'], $ticket['department_id']);

        $data = [
            'ticket_number'           => (int)$ticket['ticket_number'],
            'status'                  => $ticket['status'],
            'department'              => $ticket['department_name'] ?? null,
            'doctor'                  => $ticket['doctor_name'] ?? null,
            'room'                    => $ticket['room_name'] ?? null,
            'current_calling_number'  => $currentCalling,
            'estimated_waiting_count' => $estimatedWaitingCount,
            'updated_at'              => $ticket['called_at'] ?? $ticket['check_in_at'] ?? date('Y-m-d H:i:s')
        ];

        return $this->sendSuccess($data, 'Lấy số thứ tự hàng chờ khám thành công');
    }
}
