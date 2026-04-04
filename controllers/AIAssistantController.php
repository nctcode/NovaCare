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
                'advice' => 'Có thể do căng thẳng, thiếu ngủ, hoặc migraine. Nghỉ ngơi, uống nhiều nước. Nếu đau kéo dài hơn 3 ngày, hãy đến gặp bác sĩ Thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['sốt', 'fever', 'nóng', 'ớn lạnh'],
                'condition' => 'Sốt / Cảm cúm',
                'advice' => 'Có thể là cảm cúm hoặc nhiễm virus. Uống nhiều nước, nghỉ ngơi, có thể dùng Paracetamol. Nếu sốt > 39°C hoặc kéo dài > 3 ngày, cần đi khám ngay.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['đau ngực', 'tức ngực', 'chest pain', 'tim đập nhanh', 'khó thở'],
                'condition' => 'Vấn đề Tim mạch',
                'advice' => '⚠️ ĐÂY CÓ THỂ NGHIÊM TRỌNG! Đau ngực kèm khó thở cần được khám ngay. Có thể liên quan đến tim mạch. Hãy đến phòng cấp cứu hoặc gặp bác sĩ Tim mạch ngay lập tức.',
                'department' => 'Tim mạch',
                'severity' => 'high',
            ],
            [
                'keywords' => ['đau bụng', 'tiêu chảy', 'buồn nôn', 'nôn', 'đau dạ dày'],
                'condition' => 'Rối loạn tiêu hóa',
                'advice' => 'Có thể do thức ăn, viêm dạ dày, hoặc rối loạn tiêu hóa. Ăn nhẹ, tránh đồ cay nóng. Nếu kèm sốt hoặc đau dữ dội, cần đi khám.',
                'department' => 'Nội tổng quát',
                'severity' => 'low',
            ],
            [
                'keywords' => ['ho', 'đau họng', 'viêm họng', 'cough', 'sổ mũi', 'nghẹt mũi'],
                'condition' => 'Viêm đường hô hấp trên',
                'advice' => 'Có thể là cảm lạnh, viêm họng, hoặc viêm phế quản. Nghỉ ngơi, uống nước ấm, súc miệng nước muối. Nếu ho kéo dài > 2 tuần, cần gặp bác sĩ.',
                'department' => 'Nội tổng quát',
                'severity' => 'low',
            ],
            [
                'keywords' => ['đau lưng', 'đau cơ', 'đau khớp', 'back pain', 'nhức mỏi'],
                'condition' => 'Đau cơ xương khớp',
                'advice' => 'Có thể do tư thế sai, thoát vị đĩa đệm, hoặc viêm khớp. Chườm nóng, nghỉ ngơi. Nếu đau kéo dài hoặc tê bì chân tay, cần đi khám chuyên khoa.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['mệt mỏi', 'kiệt sức', 'chóng mặt', 'hoa mắt', 'fatigue'],
                'condition' => 'Thiếu máu / Suy nhược',
                'advice' => 'Có thể do thiếu máu, thiếu vitamin, stress, hoặc rối loạn giấc ngủ. Bổ sung dinh dưỡng, nghỉ ngơi đủ. Nên xét nghiệm máu để kiểm tra.',
                'department' => 'Nội tổng quát',
                'severity' => 'low',
            ],
            [
                'keywords' => ['mất ngủ', 'khó ngủ', 'insomnia', 'lo âu', 'stress', 'trầm cảm'],
                'condition' => 'Rối loạn giấc ngủ / Tâm lý',
                'advice' => 'Có thể liên quan đến stress, lo âu, hoặc trầm cảm. Hạn chế caffeine, tập thể dục đều đặn, thử thiền. Nếu kéo dài, nên gặp bác sĩ Tâm thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
            ],
            [
                'keywords' => ['dị ứng', 'ngứa', 'nổi mẩn', 'phát ban', 'allergy'],
                'condition' => 'Dị ứng / Phản ứng da',
                'advice' => 'Có thể do dị ứng thực phẩm, thuốc, hoặc môi trường. Tránh tác nhân gây dị ứng. Nếu kèm khó thở hoặc sưng mặt, cần cấp cứu ngay!',
                'department' => 'Nội tổng quát',
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
