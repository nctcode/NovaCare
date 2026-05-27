<!-- Dashboard Admin - NovaCare Smart Hospital 4.0 -->

<!-- Welcome and AI Operational Health Header -->
<div class="row g-4 mb-4" data-aos="fade-down">
    <div class="col-12">
        <div class="p-4 shadow-sm border-0 position-relative overflow-hidden" 
             style="border-radius: 16px; background: linear-gradient(135deg, #1e3a8a, #0f172a); color: #ffffff;">
            <!-- Decorative light circles -->
            <div class="position-absolute" style="width: 300px; height: 300px; border-radius: 50%; background: rgba(59, 130, 246, 0.12); top: -100px; right: -50px; filter: blur(30px); pointer-events: none;"></div>
            <div class="position-absolute" style="width: 150px; height: 150px; border-radius: 50%; background: rgba(14, 165, 233, 0.15); bottom: -50px; right: 100px; filter: blur(20px); pointer-events: none;"></div>
            
            <div class="row align-items-center position-relative">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; border-radius: 8px; background: rgba(59, 130, 246, 0.2) !important; color: #60a5fa !important;">
                            <i class="fa-solid fa-microchip me-1"></i> NOVACARE SMART CORE v4.0
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; border-radius: 8px; background: rgba(34, 197, 94, 0.2) !important; color: #4ade80 !important;">
                            <i class="fa-solid fa-shield-halved me-1"></i> SSL SECURE
                        </span>
                    </div>
                    <h3 class="fw-bold mb-2">Xin chào trở lại, Quản trị viên!</h3>
                    <p class="text-white-50 mb-0" style="font-size: 14px; max-width: 650px;">
                        Chào mừng bạn đến với bảng điều khiển trung tâm NovaCare. Hệ thống quản trị y tế tích hợp AI và mạng lưới cổng kết nối IoT của bệnh viện đang vận hành ổn định.
                    </p>
                </div>
                
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <div class="p-3 border border-secondary border-opacity-25 shadow-inner" style="border-radius: 12px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(5px);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50" style="font-size: 12.5px;">Chỉ số Vận hành AI</span>
                            <span class="badge bg-success" style="font-size: 10px; border-radius: 4px;">TỐT GẦN ĐÂY</span>
                        </div>
                        <div class="d-flex align-items-center gap-2.5">
                            <h3 class="fw-bold text-success mb-0 m-0">99.8%</h3>
                            <div style="font-size: 11px; line-height: 1.3;" class="text-white-50">
                                Đồng bộ IoT: <strong class="text-white">0.08s</strong><br>
                                AI Beeknoee: <strong class="text-white">Sẵn sàng</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.1);">
            
            <!-- Trợ lý AI Quick Summary Block -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-primary text-white" style="width: 32px; height: 32px; border-radius: 8px; background-color: var(--primary) !important; flex-shrink: 0;">
                    <i class="fa-solid fa-brain" style="font-size: 13px;"></i>
                </div>
                <div style="font-size: 13px;" class="text-white-50">
                    <strong class="text-white">Nhận định từ AI:</strong> Hệ thống ghi nhận các lượt hẹn hôm nay diễn ra đúng tiến độ. Cảnh báo tồn kho <code><?= count($lowStockMedicines ?? []) ?></code> loại dược phẩm cần lưu ý đặt thêm. Thiết bị y tế đang được bảo trì an toàn.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistical Summary Counters with Trend Indicators -->
<div class="row g-3 mb-4">
    <!-- Total Patients -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Tổng bệnh nhân</span>
                <div class="d-flex align-items-center justify-content-center text-primary" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); font-size: 15px;">
                    <i class="fa-solid fa-hospital-user"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalPatients'] ?? 0 ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #16a34a; font-weight: 500;">
                <i class="fa-solid fa-arrow-trend-up"></i> +5% so với tháng trước
            </div>
        </div>
    </div>

    <!-- Total Doctors -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Tổng Bác sĩ</span>
                <div class="d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(34, 197, 94, 0.08); font-size: 15px;">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalDoctors'] ?? 0 ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                <i class="fa-solid fa-minus"></i> Vận hành ổn định
            </div>
        </div>
    </div>

    <!-- Total Nurses -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Tổng Y tá</span>
                <div class="d-flex align-items-center justify-content-center text-info" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(14, 165, 233, 0.08); font-size: 15px;">
                    <i class="fa-solid fa-user-nurse"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalNurses'] ?? 0 ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                <i class="fa-solid fa-minus"></i> Bố trí đúng ca trực
            </div>
        </div>
    </div>

    <!-- Appointments -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Lịch đặt hẹn</span>
                <div class="d-flex align-items-center justify-content-center text-warning" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(245, 158, 11, 0.08); font-size: 15px;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalAppointments'] ?? 0 ?>">0</h3>
            <?php $pendingPct = ($data['totalAppointments'] ?? 0) > 0 ? round(($data['pendingAppointments'] ?? 0) / $data['totalAppointments'] * 100) : 0; ?>
            <div class="mt-auto" style="font-size: 11px; color: <?= $pendingPct > 30 ? '#ef4444' : '#16a34a' ?>; font-weight: 500;">
                <i class="fa-solid fa-clock"></i> <?= $pendingPct ?>% đang chờ khám
            </div>
        </div>
    </div>

    <!-- Medicines -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Loại thuốc</span>
                <div class="d-flex align-items-center justify-content-center text-danger" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(239, 110, 110, 0.08); font-size: 15px; color: #ef4444 !important;">
                    <i class="fa-solid fa-capsules"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalMedicines'] ?? 0 ?>">0</h3>
            <?php $lowCount = count($lowStockMedicines ?? []); ?>
            <div class="mt-auto" style="font-size: 11px; color: <?= $lowCount > 0 ? '#ef4444' : '#16a34a' ?>; font-weight: 500;">
                <?php if ($lowCount > 0): ?>
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $lowCount ?> loại cần đặt thêm
                <?php else: ?>
                    <i class="fa-solid fa-circle-check"></i> Đủ lượng tồn kho
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Equipment & Devices -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 12.5px;">Trang thiết bị</span>
                <div class="d-flex align-items-center justify-content-center text-secondary" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(100, 116, 139, 0.08); font-size: 15px;">
                    <i class="fa-solid fa-toolbox"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $data['totalEquipment'] ?? 0 ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                <i class="fa-solid fa-circle-nodes"></i> Đầy đủ kiểm định
            </div>
        </div>
    </div>
</div>

<!-- Operations & Departments Status (Real database statistics) -->
<div class="row g-4 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <div>
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-square-poll-vertical text-primary"></i> 
                        Trung tâm Chỉ đạo & Giám sát Vận hành Thực tế (NovaCare Live Operations)
                    </h6>
                    <small class="text-muted">Các chỉ số thống kê dựa trên dữ liệu hoạt động thực của bệnh viện</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; border-radius: 8px; background: rgba(59, 130, 246, 0.08) !important;">
                        <i class="fa-solid fa-database me-1"></i> Dữ liệu đồng bộ trực tiếp
                    </span>
                </div>
            </div>

            <div class="row g-3">
                <!-- Clinical Departments Count -->
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--gray-50);">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Phòng ban & Khoa lâm sàng</small>
                            <strong class="text-dark fs-6"><?= $data['totalDepartments'] ?? 0 ?> Khoa chuyên môn</strong>
                            <span class="d-block text-success mt-0.5" style="font-size: 10px;"><i class="fa-solid fa-circle text-success me-1" style="font-size: 6px;"></i>Đang mở cửa tiếp đón</span>
                        </div>
                        <div class="p-2 bg-info-subtle text-info rounded-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-building-user" style="font-size: 16px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Inpatients Count -->
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--gray-50);">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Bệnh nhân nội trú tích cực</small>
                            <strong class="text-dark fs-6"><?= $data['totalInpatients'] ?? 0 ?> Bệnh nhân</strong>
                            <span class="d-block text-primary mt-0.5" style="font-size: 10px;"><i class="fa-solid fa-circle text-primary me-1" style="font-size: 6px;"></i>Đang nằm viện điều trị</span>
                        </div>
                        <div class="p-2 bg-primary-subtle text-primary rounded-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-bed-pulse" style="font-size: 16px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Medical Devices Availability Rate -->
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--gray-50);">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Thiết bị sẵn sàng (Tỷ lệ)</small>
                            <?php 
                            $devicesRate = ($data['totalDevices'] ?? 0) > 0 ? round(($data['availableDevices'] ?? 0) / $data['totalDevices'] * 100) : 0;
                            ?>
                            <strong class="text-dark fs-6"><?= $data['availableDevices'] ?? 0 ?> / <?= $data['totalDevices'] ?? 0 ?> thiết bị</strong>
                            <span class="d-block text-success mt-0.5" style="font-size: 10px;"><i class="fa-solid fa-circle text-success me-1" style="font-size: 6px;"></i>Sẵn sàng sử dụng: <?= $devicesRate ?>%</span>
                        </div>
                        <div class="p-2 bg-success-subtle text-success rounded-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-laptop-medical" style="font-size: 16px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Medicines Catalog Stock Status -->
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between" style="background: var(--gray-50);">
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Danh mục dược phẩm</small>
                            <strong class="text-dark fs-6"><?= $data['totalMedicines'] ?? 0 ?> Đầu thuốc</strong>
                            <span class="d-block text-warning mt-0.5" style="font-size: 10px; color: #d97706 !important;"><i class="fa-solid fa-circle text-warning me-1" style="font-size: 6px;"></i>Có <?= count($lowStockMedicines ?? []) ?> thuốc cần đặt thêm</span>
                        </div>
                        <div class="p-2 bg-warning-subtle text-warning rounded-3" style="color: #d97706 !important; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-prescription-bottle-medical" style="font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Administration Actions -->
<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-bolt text-warning"></i>
                Hành động nhanh Quản trị viên
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?page=doctors&action=create" class="btn btn-outline-primary px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-user-doctor me-1.5"></i> Thêm Bác sĩ
                </a>
                <a href="index.php?page=nurses&action=create" class="btn btn-outline-success px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-user-nurse me-1.5"></i> Thêm Y tá
                </a>
                <a href="index.php?page=departments&action=create" class="btn btn-outline-info" style="border-radius: 10px; padding: 8px 16px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-building-columns me-1.5"></i> Tạo Khoa mới
                </a>
                <a href="index.php?page=shifts&action=create" class="btn btn-outline-warning" style="border-radius: 10px; padding: 8px 16px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-clock me-1.5"></i> Tạo Ca trực mới
                </a>
                <a href="index.php?page=ai-admin" class="btn btn-primary px-4 py-2 text-white" style="border-radius: 10px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-brain me-1.5"></i> Đi tới AI Quản trị
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Activity Feed -->
<div class="row g-4 mb-4">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="chart-card p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;">
            <h6 class="mb-4 fw-bold text-dark"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Đồ thị Phân tích lịch hẹn & Lượt đăng ký bệnh nhân</h6>
            <div class="row g-4">
                <div class="col-md-6 text-center">
                    <small class="text-secondary fw-semibold d-block mb-3" style="font-size: 12px;">TRẠNG THÁI LỊCH ĐẶT HẸN</small>
                    <canvas id="appointmentChart" height="220" style="max-height: 220px;"></canvas>
                </div>
                <div class="col-md-6 text-center">
                    <small class="text-secondary fw-semibold d-block mb-3" style="font-size: 12px;">XU HƯỚNG BỆNH NHÂN ĐĂNG KÝ HỒ SƠ</small>
                    <canvas id="patientChart" height="220" style="max-height: 220px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent System Activity logs & Patients -->
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="content-card p-4 border-0 shadow-sm bg-white d-flex flex-column" style="border-radius: 16px; height: 100%; max-height: 380px; overflow: hidden;">
            <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-rss me-2 text-warning"></i>Hoạt động mới cập nhật</h6>
            
            <div class="flex-grow-1 overflow-y-auto px-1" style="max-height: 280px; scrollbar-width: thin;">
                <div class="timeline" style="border-left: 2px solid var(--gray-200); padding-left: 20px; margin-left: 8px;">
                    <?php if(!empty($recentPatients)): ?>
                        <?php foreach($recentPatients as $rp): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; background: var(--primary); border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);"></span>
                            <div style="font-size: 13.5px; line-height: 1.4;">
                                Hồ sơ bệnh nhân <strong class="text-dark"><?= htmlspecialchars($rp['name']) ?></strong> vừa được khởi tạo thành công.
                            </div>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px;"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rp['created_at'])) ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(!empty($recentAppointments)): ?>
                        <?php foreach($recentAppointments as $ra): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute" style="left: -29px; top: 3px; width: 14px; height: 14px; background: var(--success); border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);"></span>
                            <div style="font-size: 13.5px; line-height: 1.4;">
                                Lịch đặt hẹn khám của <strong class="text-dark"><?= htmlspecialchars($ra['patient_name']) ?></strong> chuyển sang: 
                                <span class="badge" style="font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px; background-color: rgba(34,197,94,0.1); color: #22c55e;"><?= ucfirst($ra['status']) ?></span>
                            </div>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px;"><i class="fa-regular fa-calendar me-1"></i>Ngày hẹn: <?= date('d/m/Y', strtotime($ra['appointment_date'])) ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(!empty($lowStockMedicines)): ?>
                        <?php foreach($lowStockMedicines as $lsm): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute d-flex align-items-center justify-content-center" style="left: -29px; top: 3px; width: 14px; height: 14px; background: var(--danger); border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);"></span>
                            <div style="font-size: 13.5px; line-height: 1.4; font-weight: 500;" class="text-danger">
                                Cảnh báo kho thuốc: Dược phẩm <span class="text-dark fw-bold"><?= htmlspecialchars($lsm['name']) ?></span> sắp hết.
                            </div>
                            <small class="text-danger d-block mt-0.5" style="font-size: 11px;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chỉ còn lại <?= $lsm['quantity'] ?> hộp trong kho</small>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments Widget -->
<?php
$todayAppts = [];
if (!empty($recentAppointments)) {
    $today = date('Y-m-d');
    foreach ($recentAppointments as $ra) {
        if (isset($ra['appointment_date']) && date('Y-m-d', strtotime($ra['appointment_date'])) === $today) {
            $todayAppts[] = $ra;
        }
    }
}
?>
<div class="row g-4 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card p-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-regular fa-calendar-days text-primary"></i> 
                    Danh sách lượt lịch hẹn trong ngày hôm nay
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 12px; border-radius: 8px; font-weight: 600;">
                        <?= date('d/m/Y') ?>
                    </span>
                </h5>
                <a href="index.php?page=appointments" class="btn btn-sm btn-outline-primary px-3 py-1.5" style="border-radius: 8px; font-weight: 600; font-size: 12.5px;">Xem tất cả</a>
            </div>
            
            <div class="today-appointments-widget">
                <?php if (empty($todayAppts)): ?>
                    <div class="empty-state py-5 text-center text-muted">
                        <div class="empty-icon fs-2 text-secondary opacity-25 mb-2"><i class="fa-regular fa-calendar-check"></i></div>
                        <h6 class="fw-bold text-secondary">Không có lịch hẹn khám hôm nay</h6>
                        <p class="small text-muted mb-0">Chưa có lịch hẹn nào được lên kế hoạch cho ngày <?= date('d/m/Y') ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach (array_slice($todayAppts, 0, 6) as $ta): ?>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-light-hover d-flex align-items-center gap-3 transition-all" style="background: var(--gray-50); border-color: var(--gray-200);">
                                <div class="d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59, 130, 246, 0.08); font-size: 13.5px; flex-shrink: 0;">
                                    <?= date('H:i', strtotime($ta['appointment_date'])) ?>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <strong class="text-dark d-block text-truncate" style="font-size: 14px;"><?= htmlspecialchars($ta['patient_name'] ?? 'Bệnh nhân') ?></strong>
                                    <small class="text-muted d-block text-truncate" style="font-size: 11.5px;"><i class="fa-solid fa-user-doctor me-1"></i>BS: <?= htmlspecialchars($ta['doctor_name'] ?? 'Chưa chỉ định') ?></small>
                                </div>
                                <?php
                                $taStatusClass = 'bg-secondary-subtle text-secondary';
                                if ($ta['status'] === 'confirmed') $taStatusClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                elseif ($ta['status'] === 'completed') $taStatusClass = 'bg-success-subtle text-success border border-success-subtle';
                                elseif ($ta['status'] === 'cancelled') $taStatusClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                ?>
                                <span class="badge text-uppercase <?= $taStatusClass ?>" style="font-size: 9.5px; font-weight: 600; padding: 4px 6px; border-radius: 4px;"><?= ucfirst($ta['status']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart init script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const tickColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';

    const c1 = initAppointmentChart(
        <?= $data['pendingAppointments'] ?? 0 ?>,
        <?= $data['confirmedAppointments'] ?? 0 ?>,
        <?= $data['completedAppointments'] ?? 0 ?>,
        <?= $data['cancelledAppointments'] ?? 0 ?>
    );

    const c2 = initPatientChart(
        ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
        [24,30,18,25,32,28,45,40,<?= $data['totalPatients'] ?? 50 ?>,0,0,0]
    );

    // Register charts globally for dark mode toggling
    window._ncCharts = [c1, c2];
});
</script>
