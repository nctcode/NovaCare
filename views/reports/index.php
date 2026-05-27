<!-- Trang Báo cáo & Thống kê (Director) -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tổng quan -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-hospital-user"></i></div>
            <div class="stat-value"><?= number_format($stats['total_patients'] ?? 0) ?></div>
            <div class="stat-label">Tổng Bệnh nhân</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="stat-value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Tổng doanh thu (VNĐ)</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="stat-value"><?= $stats['total_doctors'] ?? 0 ?></div>
            <div class="stat-label">Bác sĩ</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-user-nurse"></i></div>
            <div class="stat-value"><?= $stats['total_nurses'] ?? 0 ?></div>
            <div class="stat-label">Y tá / Điều dưỡng</div>
        </div>
    </div>
</div>

<!-- Biểu đồ -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-chart-line me-2"></i>Doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
                <canvas id="reportRevenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-chart-pie me-2"></i>Lịch hẹn tháng này</h5>
            </div>
            <div class="card-body">
                <canvas id="reportApptChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- BN theo khoa -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-building-columns me-2"></i>Bệnh nhân theo Khoa</h5>
            </div>
            <div class="card-body">
                <canvas id="reportDeptChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Top BS -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-trophy me-2"></i>Top Bác sĩ (30 ngày)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($topDoctors)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr><th>#</th><th>Bác sĩ</th><th>Chuyên khoa</th><th>Lượt khám</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topDoctors as $idx => $doc): ?>
                            <tr>
                                <td><?php echo ['🥇','🥈','🥉'][$idx] ?? ($idx+1); ?></td>
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
</div>

<!-- Công suất giường & Cảnh báo -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-bed me-2"></i>Công suất giường</h5>
            </div>
            <div class="card-body text-center">
                <canvas id="reportBedChart" height="200"></canvas>
                <div class="mt-3">
                    <span class="badge bg-primary fs-6"><?= $bedOccupancy['occupancy_rate'] ?? 0 ?>%</span>
                    <p class="text-muted mt-1 mb-0"><?= $bedOccupancy['occupied_beds'] ?? 0 ?> / <?= $bedOccupancy['total_beds'] ?? 0 ?> giường</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-triangle-exclamation me-2"></i>Cảnh báo hệ thống</h5>
            </div>
            <div class="card-body">
                <?php $hasAlert = false; ?>
                <?php if (($stats['low_stock_medicines'] ?? 0) > 0): $hasAlert = true; ?>
                <div class="alert alert-danger alert-custom mb-2">
                    <i class="fa-solid fa-capsules"></i>
                    <strong><?= $stats['low_stock_medicines'] ?></strong> loại thuốc có tồn kho thấp (≤ 10 đơn vị)
                </div>
                <?php endif; ?>
                <?php if (($stats['pending_lab_orders'] ?? 0) > 0): $hasAlert = true; ?>
                <div class="alert alert-warning alert-custom mb-2">
                    <i class="fa-solid fa-flask-vial"></i>
                    <strong><?= $stats['pending_lab_orders'] ?></strong> chỉ định CLS đang chờ xử lý
                </div>
                <?php endif; ?>
                <?php if (($bedOccupancy['occupancy_rate'] ?? 0) > 90): $hasAlert = true; ?>
                <div class="alert alert-danger alert-custom mb-2">
                    <i class="fa-solid fa-bed-pulse"></i>
                    Công suất giường đạt <strong><?= $bedOccupancy['occupancy_rate'] ?>%</strong> — gần hết chỗ
                </div>
                <?php endif; ?>
                <?php if (!$hasAlert): ?>
                <div class="text-center text-muted py-3">
                    <i class="fa-solid fa-shield-check fa-2x mb-2" style="opacity:.3"></i>
                    <p>Hệ thống hoạt động bình thường</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revData = <?= json_encode($revenueByMonth ?? []) ?>;
    new Chart(document.getElementById('reportRevenueChart'), {
        type: 'bar',
        data: {
            labels: revData.map(r => r.month),
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: revData.map(r => r.revenue),
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => (v/1000000).toFixed(1) + 'M' } }, x: { grid: { display: false } } }
        }
    });

    const apptData = <?= json_encode($appointmentsByStatus ?? []) ?>;
    const sLabels = { pending:'Chờ xác nhận', confirmed:'Đã xác nhận', completed:'Hoàn thành', cancelled:'Đã hủy', emergency:'Cấp cứu' };
    const sColors = { pending:'#f59e0b', confirmed:'#3b82f6', completed:'#22c55e', cancelled:'#ef4444', emergency:'#dc2626' };
    new Chart(document.getElementById('reportApptChart'), {
        type: 'doughnut',
        data: {
            labels: apptData.map(a => sLabels[a.status] || a.status),
            datasets: [{ data: apptData.map(a => a.total), backgroundColor: apptData.map(a => sColors[a.status] || '#94a3b8'), borderWidth: 0 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    const deptData = <?= json_encode($patientsByDept ?? []) ?>;
    new Chart(document.getElementById('reportDeptChart'), {
        type: 'bar',
        data: {
            labels: deptData.map(d => d.department_name),
            datasets: [{
                label: 'Bệnh nhân',
                data: deptData.map(d => d.patient_count),
                backgroundColor: ['#6366f1','#ec4899','#f59e0b','#22c55e','#3b82f6','#8b5cf6'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });

    const bedOcc = <?= json_encode($bedOccupancy ?? ['occupied_beds'=>0,'total_beds'=>1]) ?>;
    new Chart(document.getElementById('reportBedChart'), {
        type: 'doughnut',
        data: {
            labels: ['Đang sử dụng', 'Trống'],
            datasets: [{ data: [bedOcc.occupied_beds, Math.max(0, bedOcc.total_beds - bedOcc.occupied_beds)], backgroundColor: ['#6366f1','#e5e7eb'], borderWidth: 0, cutout: '70%' }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
