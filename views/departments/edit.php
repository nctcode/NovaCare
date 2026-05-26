<!-- Form Sửa Khoa -->
<div class="row justify-content-center" data-aos="fade-up">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 text-center">
                <div class="icon-box rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,193,7,0.2)); color: #ffc107;">
                    <i class="fa-solid fa-pen-to-square fs-3"></i>
                </div>
                <h4 class="fw-bold text-dark">Cập Nhật Thông Tin Khoa</h4>
                <p class="text-muted small">Chỉnh sửa thông tin khoa <span class="fw-bold text-primary"><?= htmlspecialchars($department['name']) ?></span>.</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?page=departments&action=update">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="id" value="<?= $department['id'] ?>">
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-secondary small"><i class="fa-solid fa-signature me-1"></i>Tên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" required value="<?= htmlspecialchars($department['name']) ?>" style="border-radius: 8px;">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold text-secondary small"><i class="fa-solid fa-align-left me-1"></i>Mô tả khoa</label>
                        <textarea class="form-control bg-light border-0" id="description" name="description" rows="5" style="border-radius: 8px; resize: none;"><?= htmlspecialchars($department['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row mb-5">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-solid fa-user-doctor me-1"></i>Chọn Bác sĩ</label>
                            <div class="dropdown custom-multi-select mb-3">
                                <button class="form-select text-start bg-light border-0 dropdown-toggle-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="border-radius: 8px;">
                                    Chọn Bác sĩ...
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow border-0" style="border-radius: 8px;">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm search-select" placeholder="Tìm kiếm bác sĩ...">
                                    </div>
                                    <div class="options-container" style="max-height: 200px; overflow-y: auto;">
                                        <?php foreach ($doctors as $doctor): ?>
                                            <label class="dropdown-item d-flex align-items-center gap-2 rounded option-item">
                                                <input class="form-check-input mt-0 item-checkbox" type="checkbox" name="doctors[]" value="<?= $doctor['id'] ?>" <?= in_array($doctor['id'], $deptDoctors) ? 'checked' : '' ?>>
                                                <span class="item-text"><?= htmlspecialchars($doctor['name']) ?> (<?= htmlspecialchars($doctor['email']) ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <label class="form-label fw-bold text-primary small"><i class="fa-solid fa-star me-1"></i>Trưởng khoa</label>
                            <select name="head_doctor" class="form-select bg-light border-0" style="border-radius: 8px;">
                                <option value="">-- Không chọn --</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>" <?= ($headDoctorId == $doctor['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($doctor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small"><i class="fa-solid fa-user-nurse me-1"></i>Chọn Y tá</label>
                            <div class="dropdown custom-multi-select mb-3">
                                <button class="form-select text-start bg-light border-0 dropdown-toggle-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="border-radius: 8px;">
                                    Chọn Y tá...
                                </button>
                                <div class="dropdown-menu w-100 p-2 shadow border-0" style="border-radius: 8px;">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm search-select" placeholder="Tìm kiếm y tá...">
                                    </div>
                                    <div class="options-container" style="max-height: 200px; overflow-y: auto;">
                                        <?php foreach ($nurses as $nurse): ?>
                                            <label class="dropdown-item d-flex align-items-center gap-2 rounded option-item">
                                                <input class="form-check-input mt-0 item-checkbox" type="checkbox" name="nurses[]" value="<?= $nurse['id'] ?>" <?= in_array($nurse['id'], $deptNurses) ? 'checked' : '' ?>>
                                                <span class="item-text"><?= htmlspecialchars($nurse['name']) ?> (<?= htmlspecialchars($nurse['email']) ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <label class="form-label fw-bold text-primary small"><i class="fa-solid fa-star me-1"></i>Điều dưỡng trưởng</label>
                            <select name="head_nurse" class="form-select bg-light border-0" style="border-radius: 8px;">
                                <option value="">-- Không chọn --</option>
                                <?php foreach ($nurses as $nurse): ?>
                                    <option value="<?= $nurse['id'] ?>" <?= ($headNurseId == $nurse['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nurse['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="index.php?page=departments" class="btn btn-light px-4 py-2 text-secondary fw-bold" style="border-radius: 8px;">
                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-warning px-4 py-2 fw-bold text-white" style="border-radius: 8px; box-shadow: 0 4px 10px rgba(255, 193, 7, 0.2);">
                            <i class="fa-solid fa-save me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.custom-multi-select');
    
    dropdowns.forEach(dropdown => {
        const searchInput = dropdown.querySelector('.search-select');
        const items = dropdown.querySelectorAll('.option-item');
        const checkboxes = dropdown.querySelectorAll('.item-checkbox');
        const toggleBtn = dropdown.querySelector('.dropdown-toggle-btn');
        const defaultText = toggleBtn.innerText.trim();
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            items.forEach(item => {
                const text = item.querySelector('.item-text').innerText.toLowerCase();
                if (text.includes(term)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });
        
        // Update button text on change
        function updateButtonText() {
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            if (checkedCount > 0) {
                toggleBtn.innerHTML = `<span class="badge bg-primary me-2">${checkedCount}</span> Đã chọn`;
            } else {
                toggleBtn.innerText = defaultText;
            }
        }
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonText);
        });
        
        // Initial update
        updateButtonText();
    });
});
</script>
