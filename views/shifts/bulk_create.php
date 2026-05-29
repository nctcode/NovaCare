<!-- views/shifts/bulk_create.php -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="mb-4">
    <a href="index.php?page=shifts" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại Ca trực</a>
</div>

<div class="form-section shadow-sm p-4 rounded bg-white" data-aos="fade-up" style="border-radius: 16px; max-width: 100% !important;">
    <h5 class="mb-3 text-primary fw-bold d-flex align-items-center gap-2">
        <i class="fa-solid fa-calendar-days text-primary" style="font-size: 24px;"></i>
        Bảng Lập Lịch Trực Tuần Thông Minh
    </h5>

    <div class="alert alert-info alert-custom mb-4" style="background-color: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.2); color: #0369a1; font-size: 13.5px;">
        <i class="fa-solid fa-circle-info me-2" style="font-size: 16px;"></i>
        <strong>Tính năng lập lịch nhanh:</strong> Vui lòng tick chọn vào ca trực muốn tạo. Bạn có thể thay đổi số lượng Bác sĩ (BS) và Điều dưỡng (ĐD) trực tiếp tại mỗi ô nhập số. Để chỉnh sửa khung giờ, bấm vào biểu tượng bánh răng <i class="fa-solid fa-gear text-muted mx-0.5"></i> của ca trực tương ứng.
    </div>

    <form method="POST" action="index.php?page=shifts&action=bulkStore">
        <?= Security::csrfField() ?>
        
        <!-- Chọn tuần trực (Chọn thứ hai khởi đầu) -->
        <div class="row align-items-center mb-4">
            <div class="col-md-5">
                <label for="start_monday" class="form-label fw-bold text-dark-emphasis mb-1">Chọn Tuần áp dụng (Ngày thứ Hai đầu tuần) <span class="text-danger">*</span></label>
                <input type="date" class="form-control shadow-sm" id="start_monday" name="start_monday" required style="border-radius: 10px; padding: 10px;">
            </div>
            <div class="col-md-7 mt-3 mt-md-0 pt-md-4">
                <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-2" id="week_display" style="border: 1px solid #e2e8f0;">
                    <i class="fa-solid fa-calendar-check text-primary fs-5"></i>
                    <span class="fw-semibold text-dark">Vui lòng chọn ngày Thứ Hai đầu tuần.</span>
                </div>
            </div>
        </div>

        <!-- Bộ nút chọn nhanh tiện lợi -->
        <div class="d-flex flex-wrap gap-2 mb-4 bg-light p-2.5 rounded-3">
            <span class="fw-bold text-muted d-flex align-items-center me-2" style="font-size: 12.5px;"><i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i>Chọn nhanh:</span>
            <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="selectAllBySlot(4)" style="border-radius: 8px; font-size:12px; font-weight:600;"><i class="fa-solid fa-moon me-1"></i>Chọn Tất cả ca trực đêm</button>
            <button type="button" class="btn btn-outline-warning btn-sm px-3 text-dark" onclick="selectAllBySlot(1)" style="border-radius: 8px; font-size:12px; font-weight:600;"><i class="fa-solid fa-sun me-1"></i>Chọn Tất cả ca sáng</button>
            <button type="button" class="btn btn-outline-info btn-sm px-3" onclick="selectAllBySlot(2)" style="border-radius: 8px; font-size:12px; font-weight:600;"><i class="fa-solid fa-cloud-sun me-1"></i>Chọn Tất cả ca chiều</button>
            <button type="button" class="btn btn-sm btn-outline-secondary px-2.5" onclick="deselectAll()" style="border-radius: 8px; font-size:12px; font-weight:600;"><i class="fa-solid fa-rotate-left me-1"></i>Bỏ chọn tất cả</button>
        </div>

        <!-- Khung Lịch Dạng Hàng Ngang (Thứ 2 đến Thứ 7) -->
        <div class="d-flex flex-column gap-3 mb-4">
            <?php 
            $weekdays = [
                1 => 'Thứ Hai',
                2 => 'Thứ Ba',
                3 => 'Thứ Tư',
                4 => 'Thứ Năm',
                5 => 'Thứ Sáu',
                6 => 'Thứ Bảy'
            ];
            
            $defaultSlots = [
                1 => ['name' => 'Ca sáng thường', 'start_time' => '07:00', 'end_time' => '12:00', 'type' => 'day', 'doctors' => 1, 'nurses' => 1, 'icon' => '☀️', 'label' => 'Ca Sáng'],
                2 => ['name' => 'Ca chiều thường', 'start_time' => '12:00', 'end_time' => '17:00', 'type' => 'day', 'doctors' => 1, 'nurses' => 1, 'icon' => '🌤️', 'label' => 'Ca Chiều'],
                3 => ['name' => 'Ca tối hành chính', 'start_time' => '17:00', 'end_time' => '21:00', 'type' => 'day', 'doctors' => 1, 'nurses' => 1, 'icon' => '🌆', 'label' => 'Ca Tối'],
                4 => ['name' => 'Ca trực đêm', 'start_time' => '19:00', 'end_time' => '07:00', 'type' => 'night', 'doctors' => 2, 'nurses' => 2, 'icon' => '🌙', 'label' => 'Ca Đêm']
            ];

            foreach ($weekdays as $dayIndex => $dayName): 
            ?>
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0 !important; background-color: #ffffff;">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3">
                        <!-- Ngày Trong Tuần -->
                        <div class="col-lg-2 col-md-3 text-start text-md-center">
                            <span class="fs-6 fw-bold text-dark d-block"><?= $dayName ?></span>
                            <span class="text-muted fw-semibold date-sub-label" id="date_label_<?= $dayIndex ?>" style="font-size: 12px;">--/--/----</span>
                        </div>
                        
                        <!-- Ca Làm Việc Trong Ngày (4 Ca) -->
                        <div class="col-lg-10 col-md-9 border-start-md">
                            <div class="row g-2.5 row-cols-1 row-cols-sm-2 row-cols-lg-4">
                                <?php foreach ($defaultSlots as $slotIndex => $slot): ?>
                                <div class="col">
                                    <div class="shift-select-card p-3 rounded-3 border bg-white h-100" 
                                         id="card_<?= $dayIndex ?>_<?= $slotIndex ?>" 
                                         style="transition: all 0.2s; min-height: 125px; position: relative;">
                                        
                                        <!-- Checkbox kích hoạt & Tên Ca -->
                                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                                            <div class="form-check m-0">
                                                <input type="checkbox" class="form-check-input shift-enable-checkbox" 
                                                       id="check_<?= $dayIndex ?>_<?= $slotIndex ?>" 
                                                       name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][enabled]" 
                                                       value="1" 
                                                       data-day="<?= $dayIndex ?>" 
                                                       data-slot="<?= $slotIndex ?>"
                                                       onclick="toggleShiftCard(<?= $dayIndex ?>, <?= $slotIndex ?>)"
                                                       style="cursor: pointer;">
                                                <label class="form-check-label fw-bold text-dark ms-1" for="check_<?= $dayIndex ?>_<?= $slotIndex ?>" style="font-size: 12.5px; cursor: pointer; user-select: none;">
                                                    <?= $slot['icon'] ?> <?= $slot['label'] ?>
                                                </label>
                                            </div>
                                            
                                            <!-- Gear Chỉnh Giờ -->
                                            <button type="button" class="btn btn-link p-0 text-muted" onclick="toggleTimeEdit(<?= $dayIndex ?>, <?= $slotIndex ?>)" style="outline: none; box-shadow: none;">
                                                <i class="fa-solid fa-gear" style="font-size: 11px;"></i>
                                            </button>
                                        </div>

                                        <!-- Giờ Ca Trực -->
                                        <div id="time_text_<?= $dayIndex ?>_<?= $slotIndex ?>" class="text-primary fw-semibold mb-2" style="font-size: 12px; margin-left: 21px;">
                                            <span><?= $slot['start_time'] ?> - <?= $slot['end_time'] ?></span>
                                        </div>

                                        <!-- Hidden Inputs mẫu -->
                                        <input type="hidden" name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][name]" value="<?= htmlspecialchars($slot['name']) ?>">
                                        <input type="hidden" name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][shift_type]" value="<?= $slot['type'] ?>">

                                        <!-- Form Chỉnh Giờ (Ẩn) -->
                                        <div id="time_edit_<?= $dayIndex ?>_<?= $slotIndex ?>" class="time-edit-panel mb-2.5" style="display: none; border-top: 1px dashed #e2e8f0; padding-top: 8px; margin-left: 21px;">
                                            <div class="row g-1">
                                                <div class="col-6">
                                                    <span class="text-muted d-block" style="font-size: 8.5px;">Bắt đầu:</span>
                                                    <input type="time" class="form-control form-control-sm p-1" style="font-size: 11px; height: 26px;"
                                                           id="start_input_<?= $dayIndex ?>_<?= $slotIndex ?>"
                                                           name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][start_time]" 
                                                           value="<?= $slot['start_time'] ?>"
                                                           onchange="updateTimeText(<?= $dayIndex ?>, <?= $slotIndex ?>)">
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block" style="font-size: 8.5px;">Kết thúc:</span>
                                                    <input type="time" class="form-control form-control-sm p-1" style="font-size: 11px; height: 26px;"
                                                           id="end_input_<?= $dayIndex ?>_<?= $slotIndex ?>"
                                                           name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][end_time]" 
                                                           value="<?= $slot['end_time'] ?>"
                                                           onchange="updateTimeText(<?= $dayIndex ?>, <?= $slotIndex ?>)">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ô Nhập Số Lượng Bác Sĩ & Y Tá (Giao diện rộng, dễ chỉnh sửa) -->
                                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top" style="margin-left: 21px;">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <span class="text-muted fw-semibold" style="font-size: 11px;" title="Số Bác sĩ"><i class="fa-solid fa-user-doctor text-primary"></i> BS:</span>
                                                <input type="number" class="form-control form-control-sm text-center px-1 number-clean-input" 
                                                       name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][required_doctors]" 
                                                       min="0" value="<?= $slot['doctors'] ?>"
                                                       style="width: 44px; height: 25px; font-size: 12px; font-weight: 600; border-radius: 6px;">
                                            </div>
                                            <div class="d-flex align-items-center gap-1.5 ms-2">
                                                <span class="text-muted fw-semibold" style="font-size: 11px;" title="Số Y tá/Điều dưỡng"><i class="fa-solid fa-user-nurse text-success"></i> ĐD:</span>
                                                <input type="number" class="form-control form-control-sm text-center px-1 number-clean-input" 
                                                       name="shifts[<?= $dayIndex ?>][<?= $slotIndex ?>][required_nurses]" 
                                                       min="0" value="<?= $slot['nurses'] ?>"
                                                       style="width: 44px; height: 25px; font-size: 12px; font-weight: 600; border-radius: 6px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-4 mt-3" style="max-width: 600px;">
            <label for="notes" class="form-label fw-bold text-muted" style="font-size: 13px;">Ghi chú chung cho đợt khởi tạo:</label>
            <textarea class="form-control shadow-sm" id="notes" name="notes" rows="2" placeholder="Nhập ghi chú thêm cho ca trực nếu có..." style="border-radius: 10px;"></textarea>
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary px-4 py-2.5 text-white" style="border-radius: 10px; background-color: #0284c7; border-color: #0284c7; font-weight: 600; font-size:14px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);">
                <i class="fa-solid fa-circle-check me-1.5"></i> Khởi tạo Lịch ca trực
            </button>
            <a href="index.php?page=shifts" class="btn btn-outline-secondary px-4 py-2.5" style="border-radius: 10px; font-weight: 600; font-size:14px;">Hủy bỏ</a>
        </div>
    </form>
</div>

<style>
/* Responsive border cho cột ngăn cách */
@media (min-width: 768px) {
    .border-start-md {
        border-left: 1px solid #e2e8f0 !important;
        padding-left: 1.25rem !important;
    }
}

.shift-select-card {
    border-color: #e2e8f0 !important;
}
.shift-select-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
}
.shift-select-card.active-card {
    background-color: rgba(2, 132, 199, 0.04) !important;
    border-color: #0284c7 !important;
    box-shadow: 0 2px 10px rgba(2, 132, 199, 0.1) !important;
}

/* Ẩn hoàn toàn nút spinner tăng/giảm mặc định trên Firefox & Chrome để không chèn ô nhập số */
.number-clean-input::-webkit-outer-spin-button,
.number-clean-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.number-clean-input {
    -moz-appearance: textfield;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var mondayInput = document.getElementById('start_monday');
    var weekDisplay = document.getElementById('week_display');
    
    // Đặt mặc định là thứ Hai tuần tiếp theo
    var today = new Date();
    var nextMonday = new Date();
    nextMonday.setDate(today.getDate() + ((1 + 7 - today.getDay()) % 7 || 7));
    var yyyy = nextMonday.getFullYear();
    var mm = String(nextMonday.getMonth() + 1).padStart(2, '0');
    var dd = String(nextMonday.getDate()).padStart(2, '0');
    mondayInput.value = yyyy + '-' + mm + '-' + dd;
    
    updateWeekDisplay(nextMonday);

    mondayInput.addEventListener('change', function() {
        var selectedDate = new Date(this.value);
        
        // Ràng buộc bắt buộc chọn ngày Thứ Hai đầu tuần
        var day = selectedDate.getDay();
        if (day !== 1) { // 1 là thứ Hai
            alert('Vui lòng chọn ngày Thứ Hai đầu tuần để bắt đầu lịch biểu tuần!');
            // Chuyển về thứ hai gần nhất
            var diff = selectedDate.getDate() - day + (day === 0 ? -6 : 1);
            selectedDate.setDate(diff);
            
            var y = selectedDate.getFullYear();
            var m = String(selectedDate.getMonth() + 1).padStart(2, '0');
            var d = String(selectedDate.getDate()).padStart(2, '0');
            this.value = y + '-' + m + '-' + d;
        }
        
        updateWeekDisplay(selectedDate);
    });

    function updateWeekDisplay(dateObj) {
        var start = new Date(dateObj);
        var end = new Date(dateObj);
        end.setDate(start.getDate() + 5); // Đến thứ Bảy
        
        var startStr = start.getDate() + '/' + (start.getMonth() + 1) + '/' + start.getFullYear();
        var endStr = end.getDate() + '/' + (end.getMonth() + 1) + '/' + end.getFullYear();
        
        weekDisplay.innerHTML = `
            <i class="fa-solid fa-calendar-check text-primary fs-5"></i>
            <span class="fw-semibold text-dark">Áp dụng tuần (Thứ Hai: ${startStr} - Thứ Bảy: ${endStr})</span>
        `;

        // Cập nhật nhãn ngày cụ thể cho từng Thứ (Thứ 2 đến Thứ 7)
        for (var i = 1; i <= 6; i++) {
            var currentDay = new Date(start);
            currentDay.setDate(start.getDate() + (i - 1));
            var currentDayStr = String(currentDay.getDate()).padStart(2, '0') + '/' + String(currentDay.getMonth() + 1).padStart(2, '0');
            var label = document.getElementById('date_label_' + i);
            if (label) {
                label.textContent = currentDayStr;
            }
        }
    }
});

function toggleShiftCard(day, slot) {
    var checkbox = document.getElementById('check_' + day + '_' + slot);
    var card = document.getElementById('card_' + day + '_' + slot);
    if (checkbox.checked) {
        card.classList.add('active-card');
    } else {
        card.classList.remove('active-card');
    }
}

function toggleTimeEdit(day, slot) {
    var panel = document.getElementById('time_edit_' + day + '_' + slot);
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}

function updateTimeText(day, slot) {
    var startVal = document.getElementById('start_input_' + day + '_' + slot).value;
    var endVal = document.getElementById('end_input_' + day + '_' + slot).value;
    var timeTextSpan = document.querySelector('#time_text_' + day + '_' + slot + ' span');
    timeTextSpan.textContent = startVal + ' - ' + endVal;
}

// BỘ CHỌN NHANH TIỆN LỢI
function selectAllBySlot(slotIndex) {
    for (var d = 1; d <= 6; d++) {
        var checkbox = document.getElementById('check_' + d + '_' + slotIndex);
        if (checkbox) {
            checkbox.checked = true;
            toggleShiftCard(d, slotIndex);
        }
    }
}

function deselectAll() {
    var checkboxes = document.querySelectorAll('.shift-enable-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
        var day = checkbox.getAttribute('data-day');
        var slot = checkbox.getAttribute('data-slot');
        toggleShiftCard(day, slot);
    });
}
</script>