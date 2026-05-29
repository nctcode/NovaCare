<!-- Form Đặt lịch khám chuyên nghiệp -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="fas fa-calendar-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white fw-bold">Đặt Lịch Khám Mới</h4>
                            <p class="mb-0 text-white-50 text-sm">Vui lòng điền thông tin để đăng ký khám bệnh</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-light">
                    <form method="POST" action="index.php?page=appointments&action=store" id="createAppointmentForm" class="needs-validation" novalidate>
                        <?php echo Security::csrfField(); ?>
                        
                        <!-- Box 1: Chọn Chuyên Khoa & Bác sĩ -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-user-md me-2"></i>1. Lựa chọn Bác sĩ</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="department_filter" class="form-label text-sm fw-bold text-dark">Chuyên khoa</label>
                                    <div class="input-group input-group-outline">
                                        <select class="form-select border-radius-md px-3 border" id="department_filter">
                                            <option value="">-- Tất cả chuyên khoa --</option>
                                            <?php if(isset($departments) && is_array($departments)): ?>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?= htmlspecialchars($dept['name']) ?>"><?= htmlspecialchars($dept['name']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="form-text text-muted text-xs mt-1">Lọc bác sĩ theo chuyên khoa bạn cần khám.</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="doctor_id" class="form-label text-sm fw-bold text-dark">Chọn Bác sĩ <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <select class="form-select border-radius-md px-3 border border-primary bg-white" id="doctor_id" name="doctor_id" required>
                                            <option value="">-- Vui lòng chọn bác sĩ --</option>
                                            <?php foreach ($doctors as $d): ?>
                                                <option value="<?= $d['id'] ?>" data-dept="<?= htmlspecialchars($d['department_name'] ?? '') ?>" data-specialty="<?= htmlspecialchars($d['specialty'] ?? '') ?>">
                                                    Bác sĩ <?= htmlspecialchars($d['name']) ?> 
                                                    <?= !empty($d['department_name']) ? ' - ' . htmlspecialchars($d['department_name']) : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback text-xs">Vui lòng chọn một bác sĩ.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Box 2: Lịch trình & Lý do -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-clock me-2"></i>2. Thông tin lịch hẹn</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-12 mb-3">
                                    <label for="appointment_date_only" class="form-label text-sm fw-bold text-dark">Chọn Ngày khám <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <input type="date" class="form-control border-radius-md px-3 border border-primary bg-white" id="appointment_date_only" name="appointment_date_only" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="invalid-feedback text-xs">Vui lòng chọn ngày khám.</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label text-sm fw-bold text-dark">Chọn Khung giờ khám còn trống <span class="text-danger">*</span></label>
                                    <div id="slots_loading" class="text-muted text-xs mb-2" style="display:none;">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i>Đang tải các khung giờ khám...
                                    </div>
                                    <div id="time_slots_container" class="d-flex flex-wrap gap-2 py-2">
                                        <div class="text-muted text-xs"><i class="fa-solid fa-circle-info me-1"></i>Vui lòng chọn bác sĩ và ngày khám để xem giờ trống</div>
                                    </div>
                                    <input type="hidden" id="appointment_time_only" required>
                                    <input type="hidden" id="appointment_date" name="appointment_date" required>
                                    <div class="invalid-feedback text-xs" id="slot_error_msg" style="display:none;">Vui lòng chọn một khung giờ khám còn trống.</div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="reason" class="form-label text-sm fw-bold text-dark">Lý do khám bệnh <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <textarea class="form-control border-radius-md p-3 border border-primary bg-white" id="reason" name="reason" rows="4" required 
                                                  placeholder="Vui lòng mô tả chi tiết các triệu chứng, tiền sử bệnh hoặc lý do bạn muốn khám..."></textarea>
                                    </div>
                                    <div class="invalid-feedback text-xs">Vui lòng nhập lý do khám bệnh.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <a href="index.php?page=appointments" class="btn btn-outline-secondary btn-lg mb-0 px-4 rounded-pill shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg mb-0 px-5 rounded-pill shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Xác nhận Đặt Lịch
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for the new form */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .form-select, .form-control {
        transition: all 0.3s ease;
    }
    .form-select:focus, .form-control:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.25rem rgba(42, 82, 152, 0.25);
    }
    .invalid-feedback {
        display: none;
    }
    .was-validated .form-control:invalid, .was-validated .form-select:invalid {
        border-color: #dc3545;
    }
    .was-validated .form-control:invalid ~ .invalid-feedback, .was-validated .form-select:invalid ~ .invalid-feedback {
        display: block;
    }

    /* CSS cho Khung giờ khám */
    .slot-btn {
        flex: 1 0 calc(25% - 8px);
        min-width: 90px;
        text-align: center;
        padding: 10px 8px;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .slot-btn:hover:not(.disabled) {
        border-color: #10b981;
        color: #10b981;
        background: rgba(16, 185, 129, 0.03);
        transform: translateY(-1px);
    }

    .slot-btn.active {
        border-color: #10b981;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .slot-btn.disabled {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        text-decoration: line-through;
        opacity: 0.7;
    }
    
    .slot-btn.disabled i {
        text-decoration: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Form Validation Bootstrap
    const form = document.getElementById('createAppointmentForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // 2. Doctor filtering logic
    const deptFilter = document.getElementById('department_filter');
    const doctorSelect = document.getElementById('doctor_id');
    const originalDoctorOptions = Array.from(doctorSelect.options); // Save original options

    deptFilter.addEventListener('change', function() {
        const selectedDept = this.value;
        
        // Clear current options except the first placeholder
        doctorSelect.innerHTML = '';
        doctorSelect.appendChild(originalDoctorOptions[0]);

        // Filter and append
        let found = false;
        originalDoctorOptions.slice(1).forEach(opt => {
            const docDept = opt.getAttribute('data-dept');
            if (selectedDept === '' || docDept === selectedDept) {
                doctorSelect.appendChild(opt);
                found = true;
            }
        });
        
        // Reset selection to the first default option
        doctorSelect.selectedIndex = 0;
        
        if(!found && selectedDept !== '') {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = "";
            emptyOpt.text = "Không có bác sĩ thuộc khoa này";
            emptyOpt.disabled = true;
            doctorSelect.appendChild(emptyOpt);
        }

        // Trigger slot reload (which clears them since doctor is deselected)
        fetchBookedSlots();
    });

    // 3. Time slot selection logic
    const dateInput = document.getElementById('appointment_date_only');
    const slotsContainer = document.getElementById('time_slots_container');
    const slotsLoading = document.getElementById('slots_loading');
    const timeOnlyInput = document.getElementById('appointment_time_only');
    const fullDateInput = document.getElementById('appointment_date');

    const timeSlots = [
        // Morning
        '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        // Afternoon
        '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
    ];

    function fetchBookedSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        // Reset inputs
        timeOnlyInput.value = '';
        fullDateInput.value = '';

        if (!doctorId || !date) {
            slotsContainer.innerHTML = '<div class="text-muted text-xs"><i class="fa-solid fa-circle-info me-1"></i>Vui lòng chọn bác sĩ và ngày khám để xem giờ trống</div>';
            return;
        }

        slotsLoading.style.display = 'block';
        slotsContainer.innerHTML = '';

        fetch(`index.php?page=appointments&action=getDoctorBookedSlots&doctor_id=${doctorId}&date=${date}`)
            .then(res => res.json())
            .then(bookedTimes => {
                slotsLoading.style.display = 'none';
                slotsContainer.innerHTML = '';
                
                // Parse booked times (which are full datetimes like 'YYYY-MM-DD HH:MM:SS')
                const bookedTimestamps = bookedTimes.map(timeStr => new Date(timeStr.replace(/-/g, '/')).getTime());
                
                // Get current time to prevent booking past slots today
                const now = new Date();
                const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                const isToday = (date === todayStr);

                timeSlots.forEach(slot => {
                    const slotDateTimeStr = `${date} ${slot}:00`;
                    // Use replace for cross-browser Date parsing compatibility
                    const slotTime = new Date(slotDateTimeStr.replace(/-/g, '/')).getTime();
                    
                    // Check if this slot conflicts with any booked appointment within 30 minutes
                    let isBooked = false;
                    for (let bookedTs of bookedTimestamps) {
                        const diffMins = Math.abs(slotTime - bookedTs) / (1000 * 60);
                        if (diffMins < 30) {
                            isBooked = true;
                            break;
                        }
                    }

                    // Check if slot is in the past
                    let isPast = false;
                    if (isToday) {
                        const slotHour = parseInt(slot.split(':')[0]);
                        const slotMin = parseInt(slot.split(':')[1]);
                        const compareDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), slotHour, slotMin);
                        if (compareDate <= now) {
                            isPast = true;
                        }
                    }

                    // Create slot button
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'slot-btn';
                    
                    if (isBooked) {
                        btn.classList.add('disabled');
                        btn.innerHTML = `<i class="fa-solid fa-lock text-danger text-xs"></i> ${slot}`;
                        btn.title = "Đã có bệnh nhân đặt lịch";
                    } else if (isPast) {
                        btn.classList.add('disabled');
                        btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left text-xs"></i> ${slot}`;
                        btn.title = "Giờ khám đã trôi qua";
                    } else {
                        btn.innerHTML = `<i class="fa-regular fa-clock text-xs text-success"></i> ${slot}`;
                        btn.addEventListener('click', function() {
                            // Clear active states
                            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            
                            // Set selected time
                            timeOnlyInput.value = slot;
                            fullDateInput.value = slotDateTimeStr;
                            
                            // Clear error borders
                            document.getElementById('slot_error_msg').style.display = 'none';
                            slotsContainer.style.border = 'none';
                        });
                    }

                    slotsContainer.appendChild(btn);
                });
            })
            .catch(err => {
                slotsLoading.style.display = 'none';
                console.error("Lỗi khi tải khung giờ khám:", err);
                slotsContainer.innerHTML = '<div class="text-danger text-xs"><i class="fa-solid fa-triangle-exclamation me-1"></i>Lỗi tải khung giờ khám. Vui lòng thử lại.</div>';
            });
    }

    doctorSelect.addEventListener('change', fetchBookedSlots);
    dateInput.addEventListener('change', fetchBookedSlots);
    
    // Intercept form submission to validate time slot selection
    form.addEventListener('submit', function(event) {
        if (!timeOnlyInput.value) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById('slot_error_msg').style.display = 'block';
            slotsContainer.style.border = '1px solid #dc3545';
            slotsContainer.style.borderRadius = '12px';
            slotsContainer.style.padding = '8px';
        } else {
            document.getElementById('slot_error_msg').style.display = 'none';
            slotsContainer.style.border = 'none';
        }
    });
});
</script>
