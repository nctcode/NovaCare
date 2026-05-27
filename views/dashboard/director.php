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
