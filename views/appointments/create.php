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
                                    <label for="appointment_date" class="form-label text-sm fw-bold text-dark">Ngày và giờ khám <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-outline">
                                        <input type="datetime-local" class="form-control border-radius-md px-3 border border-primary bg-white" id="appointment_date" name="appointment_date" required min="<?= date('Y-m-d\TH:i') ?>">
                                    </div>
                                    <div class="form-text text-muted text-xs mt-1">Chọn giờ khám (trong giờ hành chính). Hệ thống sẽ kiểm tra trùng lịch.</div>
                                    <div class="invalid-feedback text-xs">Vui lòng chọn ngày giờ khám hợp lệ.</div>
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
    });
});
</script>
