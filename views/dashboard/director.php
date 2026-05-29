<!-- Dashboard Ban giám đốc -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6" data-aos="fade-up">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-hospital-user"></i></div>
            <div class="stat-value"><?= number_format($directorStats['total_patients'] ?? 0) ?></div>
            <div class="stat-label">Tổng Bệnh nhân</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-value"><?= number_format($directorStats['month_revenue'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Doanh thu tháng (VNĐ)</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= $directorStats['today_appointments'] ?? 0 ?></div>
            <div class="stat-label">Lịch hẹn hôm nay</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-bed-pulse"></i></div>
            <div class="stat-value"><?= $directorStats['current_inpatients'] ?? 0 ?></div>
            <div class="stat-label">Nội trú hiện tại</div>
        </div>
    </div>
</div>

<!-- Row 2: Nhân sự & Cảnh báo -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="stat-card" style="border-left:4px solid #6366f1">
            <div class="stat-icon" style="color:#6366f1"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="stat-value"><?= $directorStats['total_doctors'] ?? 0 ?></div>
            <div class="stat-label">Bác sĩ</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="500">
        <div class="stat-card" style="border-left:4px solid #ec4899">
            <div class="stat-icon" style="color:#ec4899"><i class="fa-solid fa-user-nurse"></i></div>
            <div class="stat-value"><?= $directorStats['total_nurses'] ?? 0 ?></div>
            <div class="stat-label">Y tá / Điều dưỡng</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="600">
        <div class="stat-card <?= ($directorStats['low_stock_medicines'] ?? 0) > 0 ? 'card-danger' : '' ?>" style="border-left:4px solid #ef4444">
            <div class="stat-icon" style="color:#ef4444"><i class="fa-solid fa-capsules"></i></div>
            <div class="stat-value"><?= $directorStats['low_stock_medicines'] ?? 0 ?></div>
            <div class="stat-label">Thuốc tồn kho thấp</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="700">
        <div class="stat-card" style="border-left:4px solid #f59e0b">
            <div class="stat-icon" style="color:#f59e0b"><i class="fa-solid fa-flask-vial"></i></div>
            <div class="stat-value"><?= $directorStats['pending_lab_orders'] ?? 0 ?></div>
            <div class="stat-label">CLS chờ xử lý</div>
        </div>
    </div>
</div>

<!-- ================= AI PREDICTIVE OPERATIONAL LOAD (4.0) ================= -->
<div class="row mb-4" data-aos="fade-up" data-aos-delay="750">
    <div class="col-12">
        <div class="content-card" style="border: 1px solid #c7d2fe; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%);">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 style="color:#4f46e5; font-weight:700;"><i class="fa-solid fa-brain me-2"></i>Dự báo Vận hành & Tải Bệnh viện (AI 4.0)</h5>
                <button type="button" class="btn btn-sm btn-primary px-3 py-2" id="btnAiPredict" style="border-radius:20px; font-weight:600; background:#4f46e5; border:none;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Khởi chạy Dự báo AI
                </button>
            </div>
            <div class="card-body">
                <div id="aiPredictPlaceholder" class="text-center py-4">
                    <p class="text-muted mb-0"><i class="fa-solid fa-circle-info me-1"></i> Nhấp nút để AI phân tích lịch hẹn 30 ngày qua, công suất giường nội trú và khoa/phòng để dự đoán lưu lượng tuần tới.</p>
                </div>
                
                <div id="aiPredictLoading" class="text-center py-4" style="display:none;">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x text-primary mb-2"></i>
                    <p class="text-muted mb-0">AI đang xử lý số liệu bệnh viện và lập mô hình dự báo...</p>
                </div>

                <div id="aiPredictResult" style="display:none;">
                    <div class="row g-4">
                        <!-- Cột 1: Chỉ số chính -->
                        <div class="col-md-4 border-end">
                            <div class="p-3 bg-white rounded-3 border mb-3">
                                <small class="text-muted fw-semibold">Tải dự kiến tuần tới</small>
                                <div class="d-flex align-items-baseline gap-2 mt-1">
                                    <h2 class="fw-bold text-primary mb-0" id="aiLoadPct">0%</h2>
                                    <span class="badge" id="aiLoadRiskBadge">Bình thường</span>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar progress-bar-striped" role="progressbar" id="aiLoadProgressBar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <div class="p-3 bg-white rounded-3 border mb-3">
                                <small class="text-muted fw-semibold"><i class="fa-solid fa-calendar-day me-1"></i>Ngày cao điểm</small>
                                <h6 class="fw-bold mt-1 mb-0" id="aiPeakDays">—</h6>
                            </div>

                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted fw-semibold"><i class="fa-solid fa-hospital me-1"></i>Khoa bận rộn nhất</small>
                                <h6 class="fw-bold mt-1 mb-0" id="aiBusiestDept">—</h6>
                            </div>
                        </div>

                        <!-- Cột 2: Biểu đồ dự báo 7 ngày -->
                        <div class="col-md-4 border-end">
                            <h6 class="fw-bold mb-3" style="color:#4f46e5;"><i class="fa-solid fa-chart-simple me-1"></i>Dự báo 7 ngày tới</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0" style="font-size:13px;">
                                    <tbody id="aiForecastTableBody">
                                        <!-- Javascript generated -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Cột 3: Phân tích & Đề xuất hành động -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-magnifying-glass-chart me-1"></i>Phân tích xu hướng</h6>
                                <p class="text-muted small lh-sm" id="aiAnalysisText" style="text-align:justify;"></p>
                            </div>
                            <div>
                                <h6 class="fw-bold text-success mb-1"><i class="fa-solid fa-lightbulb me-1"></i>Đề xuất của AI</h6>
                                <p class="text-muted small lh-sm" id="aiRecommendationsText" style="text-align:justify;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ================= END AI PREDICTIVE ================= -->

<!-- Biểu đồ & Bảng chi tiết -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ doanh thu -->
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="800">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-chart-line me-2"></i>Doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Công suất giường -->
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="900">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-bed me-2"></i>Công suất giường</h5>
            </div>
            <div class="card-body text-center">
                <canvas id="bedChart" height="200"></canvas>
                <div class="mt-3">
                    <span class="badge bg-primary fs-6"><?= $bedOccupancy['occupancy_rate'] ?? 0 ?>%</span>
                    <p class="text-muted mt-1 mb-0"><?= $bedOccupancy['occupied_beds'] ?? 0 ?> / <?= $bedOccupancy['total_beds'] ?? 0 ?> giường</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top bác sĩ -->
    <div class="col-lg-6" data-aos="fade-up">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-trophy me-2"></i>Top Bác sĩ (30 ngày)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($topDoctors)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bác sĩ</th>
                                <th>Chuyên khoa</th>
                                <th>Số lượt khám</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topDoctors as $idx => $doc): ?>
                            <tr>
                                <td>
                                    <?php if ($idx === 0): ?>
                                        <span style="color:#f59e0b;font-size:18px">🥇</span>
                                    <?php elseif ($idx === 1): ?>
                                        <span style="font-size:18px">🥈</span>
                                    <?php elseif ($idx === 2): ?>
                                        <span style="font-size:18px">🥉</span>
                                    <?php else: ?>
                                        <?= $idx + 1 ?>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($doc['name']) ?></strong></td>
                                <td><small><?= htmlspecialchars($doc['specialty'] ?? '') ?></small></td>
                                <td><span class="badge bg-primary"><?= $doc['record_count'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn theo trạng thái -->
    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-chart-pie me-2"></i>Lịch hẹn tháng này</h5>
            </div>
            <div class="card-body">
                <canvas id="appointmentChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AI Predict click
    const btnAi = document.getElementById('btnAiPredict');
    btnAi.addEventListener('click', async function() {
        document.getElementById('aiPredictPlaceholder').style.display = 'none';
        document.getElementById('aiPredictLoading').style.display = 'block';
        document.getElementById('aiPredictResult').style.display = 'none';
        btnAi.disabled = true;

        try {
            const res = await fetch('index.php?page=dashboard&action=aiPredictLoad');
            const data = await res.json();

            if (data.success && data.data) {
                const d = data.data;

                // Cập nhật Cột 1
                document.getElementById('aiLoadPct').textContent = d.predicted_load_percentage + '%';
                document.getElementById('aiPeakDays').textContent = d.peak_days.join(', ');
                document.getElementById('aiBusiestDept').textContent = d.busiest_department;

                const pb = document.getElementById('aiLoadProgressBar');
                pb.style.width = d.predicted_load_percentage + '%';
                
                const rb = document.getElementById('aiLoadRiskBadge');
                const riskMap = {
                    normal: { text: 'Bình thường', bg: '#22c55e' },
                    warning: { text: 'Cảnh báo', bg: '#f59e0b' },
                    danger: { text: 'Quá tải ⚠️', bg: '#ef4444' },
                    critical: { text: 'Nguy cấp 🚨', bg: '#dc2626' }
                };
                const risk = riskMap[d.risk_level] || { text: d.risk_level, bg: '#94a3b8' };
                rb.textContent = risk.text;
                rb.style.backgroundColor = risk.bg;

                if (d.predicted_load_percentage > 85) {
                    pb.className = 'progress-bar progress-bar-striped bg-danger';
                } else if (d.predicted_load_percentage > 60) {
                    pb.className = 'progress-bar progress-bar-striped bg-warning';
                } else {
                    pb.className = 'progress-bar progress-bar-striped bg-success';
                }

                // Cập nhật Cột 2 (Forecast)
                const tbody = document.getElementById('aiForecastTableBody');
                tbody.innerHTML = '';
                
                const statusBadges = {
                    low: { text: 'Thấp', bg: '#e0f2fe', color: '#0369a1' },
                    medium: { text: 'TB', bg: '#fef3c7', color: '#b45309' },
                    high: { text: 'Cao ⚠️', bg: '#fee2e2', color: '#b91c1c' }
                };

                d.forecast_7days.forEach(f => {
                    const sb = statusBadges[f.load_status] || { text: f.load_status, bg: '#f3f4f6', color: '#374151' };
                    tbody.innerHTML += `
                        <tr>
                            <td class="fw-semibold" style="width:30%">${f.day}</td>
                            <td style="width:40%"><span class="badge bg-light text-dark border">${f.estimated_patients} BN</span></td>
                            <td style="width:30%"><span class="badge" style="background:${sb.bg}; color:${sb.color}; font-size:11px;">${sb.text}</span></td>
                        </tr>
                    `;
                });

                // Cập nhật Cột 3
                document.getElementById('aiAnalysisText').textContent = d.analysis;
                document.getElementById('aiRecommendationsText').textContent = d.recommendations;

                document.getElementById('aiPredictResult').style.display = 'block';
            } else {
                alert('Dự báo thất bại: ' + (data.error || 'Lỗi không rõ'));
                document.getElementById('aiPredictPlaceholder').style.display = 'block';
            }
        } catch (err) {
            console.error(err);
            alert('Lỗi kết nối máy chủ AI.');
            document.getElementById('aiPredictPlaceholder').style.display = 'block';
        }

        document.getElementById('aiPredictLoading').style.display = 'none';
        btnAi.disabled = false;
    });

    // Doanh thu chart
    const revData = <?= json_encode($revenueByMonth ?? []) ?>;
    const revLabels = revData.map(r => r.month);
    const revValues = revData.map(r => r.revenue);
    
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revLabels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: revValues,
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(99, 102, 241, 0.3)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => (v/1000000).toFixed(1) + 'M' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Bed occupancy chart
    const bedOcc = <?= json_encode($bedOccupancy ?? ['occupied_beds' => 0, 'total_beds' => 1]) ?>;
    const freeB = Math.max(0, bedOcc.total_beds - bedOcc.occupied_beds);
    new Chart(document.getElementById('bedChart'), {
        type: 'doughnut',
        data: {
            labels: ['Đang sử dụng', 'Trống'],
            datasets: [{
                data: [bedOcc.occupied_beds, freeB],
                backgroundColor: ['#6366f1', '#e5e7eb'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Appointment chart
    const apptData = <?= json_encode($appointmentsByStatus ?? []) ?>;
    const statusLabels = { pending: 'Chờ xác nhận', confirmed: 'Đã xác nhận', completed: 'Hoàn thành', cancelled: 'Đã hủy', emergency: 'Cấp cứu' };
    const statusColors = { pending: '#f59e0b', confirmed: '#3b82f6', completed: '#22c55e', cancelled: '#ef4444', emergency: '#dc2626' };
    new Chart(document.getElementById('appointmentChart'), {
        type: 'doughnut',
        data: {
            labels: apptData.map(a => statusLabels[a.status] || a.status),
            datasets: [{
                data: apptData.map(a => a.total),
                backgroundColor: apptData.map(a => statusColors[a.status] || '#94a3b8'),
                borderWidth: 0
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
