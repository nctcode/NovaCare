<!-- Assign Bed to Pending Inpatient -->
<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-bed-pulse me-2 text-primary"></i>Xếp giường bệnh & Nhập viện</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Chọn phòng và giường điều trị cho bệnh nhân đã có chỉ định nhập viện</p>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=inpatient&action=storeAssignBed&id=<?= $admission['id'] ?>">
            <?php echo Security::csrfField(); ?>
            
            <div class="row g-3 mb-4 bg-light p-3 rounded" style="border-radius: 12px; margin: 0 0 20px 0;">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Bệnh nhân</label>
                    <div class="fw-bold text-dark" style="font-size: 15px;">
                        <?= htmlspecialchars($admission['patient_name']) ?> (<?= $admission['patient_phone'] ?? 'Không có SĐT' ?>)
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Bác sĩ chỉ định / phụ trách</label>
                    <div class="fw-bold text-dark" style="font-size: 15px;">
                        BS. <?= htmlspecialchars($admission['doctor_name']) ?>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Chẩn đoán ban đầu</label>
                    <div class="text-dark">
                        <?= nl2br(htmlspecialchars($admission['diagnosis'])) ?>
                    </div>
                </div>
                <?php if (!empty($admission['notes'])): ?>
                <div class="col-md-12 mt-2">
                    <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Ghi chú y khoa</label>
                    <div class="text-dark italic">
                        <?= nl2br(htmlspecialchars($admission['notes'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Phòng - Giường <span class="text-danger">*</span></label>
                    <select name="bed_id" class="form-select" required>
                        <option value="">-- Chọn giường trống --</option>
                        <?php foreach ($beds as $b): ?>
                            <option value="<?= $b['id'] ?>">
                                Phòng <?= $b['room_number'] ?> (<?= ucfirst($b['room_type']) ?>) - Giường <?= $b['bed_number'] ?> — <?= number_format($b['price_per_day'], 0, ',', '.') ?>đ/ngày
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ngày giờ nhập viện chính thức</label>
                    <input type="datetime-local" name="admission_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-check me-2"></i>Xác nhận Xếp giường & Nhập viện
                </button>
                <a href="index.php?page=inpatient" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</div>
