<?php
/**
 * AIAssistantController - Trợ lý AI y tế (keyword-based simulation)
 */

class AIAssistantController {

    public function index() {
        $response = null;
        $userInput = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userInput = trim($_POST['symptoms'] ?? '');
            if (!empty($userInput)) {
                $response = $this->analyzeSymptoms($userInput);
            }
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ai-assistant/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    private function analyzeSymptoms($input) {
        $input = mb_strtolower($input, 'UTF-8');

        // Keyword database
        $conditions = [
            [
                'keywords' => ['đau đầu', 'nhức đầu', 'headache', 'đau nửa đầu'],
                'condition' => 'Đau đầu / Migraine',
                'advice' => 'Có thể do căng thẳng, thiếu ngủ, hoặc migraine. Nghỉ ngơi, uống nhiều nước. Nếu đau kéo dài hơn 3 ngày, hãy đến gặp bác sĩ chuyên khoa Thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['đau ngực', 'tức ngực', 'chest pain', 'tim đập nhanh', 'khó thở'],
                'condition' => 'Vấn đề Tim mạch',
                'advice' => '⚠️ ĐÂY CÓ THỂ LÀ DẤU HIỆU NGHIÊM TRỌNG! Đau ngực kèm khó thở cần được xử lý ngay. Có thể liên quan đến bệnh lý tim mạch. Hãy đến phòng cấp cứu hoặc gặp bác sĩ Tim mạch ngay lập tức.',
                'department' => 'Tim mạch',
                'severity' => 'high',
            ],
            [
                'keywords' => ['sốt trẻ', 'trẻ sốt', 'child fever', 'bé sốt', 'con sốt', 'trẻ em sốt'],
                'condition' => 'Sốt ở trẻ em',
                'advice' => 'Trẻ bị sốt cần được theo dõi sát sao. Lau mát, cho bé uống nhiều nước. Nếu sốt > 38.5°C hoặc không hạ sau khi dùng thuốc, hãy đưa bé đến khám Nhi khoa ngay.',
                'department' => 'Nhi khoa',
                'severity' => 'high',
            ],
            [
                'keywords' => ['sốt', 'fever', 'nóng', 'ớn lạnh'],
                'condition' => 'Sốt / Cảm cúm',
                'advice' => 'Có thể là cảm cúm hoặc nhiễm virus. Uống nhiều nước, nghỉ ngơi, có thể dùng thuốc hạ sốt. Nếu sốt cao > 39°C kéo dài, cần đi khám bệnh viện.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['phát ban', 'nổi mẩn', 'ngứa', 'skin rash', 'nổi đỏ', 'dị ứng da'],
                'condition' => 'Viêm da / Dị ứng da',
                'advice' => 'Có thể do dị ứng thời tiết, thức ăn hoặc viêm da tiếp xúc. Tránh gãi nhiều để không gây nhiễm trùng. Nên khám chuyên khoa Da liễu để có thuốc bôi phù hợp.',
                'department' => 'Da liễu',
                'severity' => 'low',
            ],
            [
                'keywords' => ['đau bụng', 'tiêu chảy', 'buồn nôn', 'nôn', 'đau dạ dày'],
                'condition' => 'Rối loạn tiêu hóa',
                'advice' => 'Có thể do ngộ độc thức ăn, viêm dạ dày, hoặc rối loạn tiêu hóa. Ăn chín uống sôi, bù nước. Nếu đau dữ dội, cần gặp bác sĩ Nội Tiêu hóa.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['ho', 'đau họng', 'viêm họng', 'cough', 'sổ mũi', 'nghẹt mũi'],
                'condition' => 'Viêm đường hô hấp trên',
                'advice' => 'Có thể là cảm lạnh, viêm họng, hoặc viêm phế quản. Nghỉ ngơi, súc miệng nước muối. Nếu ho kéo dài > 2 tuần hoặc khó thở, cần gặp bác sĩ Nội Hô hấp.',
                'department' => 'Nội tổng quát',
                'severity' => 'low',
            ],
            [
                'keywords' => ['đau lưng', 'đau cơ', 'đau khớp', 'back pain', 'nhức mỏi'],
                'condition' => 'Đau cơ xương khớp',
                'advice' => 'Có thể do sai tư thế, thoát vị đĩa đệm, hoặc viêm khớp. Chườm nóng, nghỉ ngơi. Nếu đau lan xuống chân, cần đi khám chuyên khoa Cơ xương khớp / Chấn thương chỉnh hình.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['mất ngủ', 'khó ngủ', 'insomnia', 'lo âu', 'stress', 'trầm cảm'],
                'condition' => 'Rối loạn giấc ngủ / Tâm lý',
                'advice' => 'Có thể do căng thẳng hoặc rối loạn tâm lý. Hạn chế thiết bị điện tử trước ngủ. Nếu mất ngủ kéo dài làm suy nhược, nên gặp bác sĩ Tâm lý / Thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
            ],
        ];

        foreach ($conditions as $cond) {
            foreach ($cond['keywords'] as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $cond;
                }
            }
        }

        return [
            'condition' => 'Không xác định',
            'advice' => 'Xin lỗi, tôi không thể xác định chính xác tình trạng từ mô tả của bạn. Vui lòng mô tả chi tiết hơn hoặc đặt lịch khám với bác sĩ để được tư vấn trực tiếp.',
            'department' => 'Nội tổng quát',
            'severity' => 'low',
        ];
    }
}
