<?php
// Lấy thời gian để tính lời chào
$hour = date('H');
$greeting = 'Chào buổi sáng';
if ($hour >= 12 && $hour < 18) $greeting = 'Chào buổi chiều';
if ($hour >= 18) $greeting = 'Chào buổi tối';

$user = $_SESSION['user'];
$recentAppointments = $data['recentAppointments'] ?? [];
?>

<!-- Admin Dashboard CSS Inline for simplicity & localized styling -->
<style>
.dashboard-banner {
    background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
    border-radius: var(--radius-xl);
    padding: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);
}
.dashboard-banner::after {
    content: ''; position: absolute; top: -50%; right: -10%;
    width: 300px; height: 300px; background: rgba(255,255,255,0.1);
    border-radius: 50%;
}
.dashboard-banner h3 { font-weight: 700; margin-bottom: 8px; font-size: 24px; position: relative; z-index: 2;}
.dashboard-banner p { margin: 0; opacity: 0.9; position: relative; z-index: 2;}

.admin-metric-card {
    background: white; border-radius: var(--radius-lg); padding: 24px;
    border: 1px solid var(--gray-100);
    box-shadow: var(--shadow-sm); transition: var(--transition);
    display: flex; align-items: center; justify-content: space-between;
}
.admin-metric-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: var(--primary-light); }
.admin-metric-card .metric-info h6 { color: var(--gray-500); font-size: 14px; margin-bottom: 8px; font-weight: 500;}
.admin-metric-card .metric-info h3 { color: var(--dark); font-size: 28px; font-weight: 800; margin: 0;}
.admin-metric-card .metric-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;}
.metric-icon.blue { background: #eff6ff; color: #3b82f6; }
.metric-icon.green { background: #f0fdf4; color: #22c55e; }
.metric-icon.orange { background: #fff7ed; color: #f97316; }
.metric-icon.purple { background: #faf5ff; color: #a855f7; }

.recent-list-card { background: white; border-radius: var(--radius-lg); border: 1px solid var(--gray-100); box-shadow: var(--shadow-sm); overflow: hidden; height: 100%;}
.recent-list-header { padding: 20px 24px; border-bottom: 1px solid var(--gray-100); display: flex; justify-content: space-between; align-items: center; }
.recent-list-header h5 { margin: 0; font-weight: 600; font-size: 16px; color: var(--dark); }
.recent-item { padding: 16px 24px; border-bottom: 1px solid var(--gray-50); display: flex; align-items: center; gap: 16px; transition: background 0.2s; }
.recent-item:hover { background: var(--gray-50); }
.recent-item:last-child { border-bottom: none; }
.recent-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink:0;}
.recent-info { flex-grow: 1; min-width: 0;}
.recent-info p { margin: 0; font-weight: 500; color: var(--dark); font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
.recent-info small { color: var(--gray-500); font-size: 12px; }

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap;}
.status-pending { background: #fef3c7; color: #d97706; }
.status-confirmed { background: #dbeafe; color: #2563eb; }
.status-completed { background: #dcfce7; color: #16a34a; }
.status-cancelled { background: #fee2e2; color: #dc2626; }

.quick-action-btn { background: white; border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 16px; display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--dark); transition: var(--transition); font-weight: 500;}
.quick-action-btn:hover { background: var(--primary-50); border-color: var(--primary); color: var(--primary); transform: translateY(-2px);}
.quick-action-btn i { font-size: 24px; color: var(--primary); }
</style>

<!-- Welcome Banner -->
<div class="dashboard-banner" data-aos="fade-in">
    <h3><?= $greeting ?>, <?= htmlspecialchars($user['name']) ?>! 👋</h3>
    <p>Hôm nay là <?= date('d/m/Y') ?>. Hệ thống đang hoạt động ổn định.</p>
</div>

<!-- Key Metrics Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6" data-aos="fade-up">
        <div class="admin-metric-card">
            <div class="metric-info">
                <h6>Tổng Bệnh Nhân</h6>
                <h3><?= number_format($data['totalPatients'] ?? 0) ?></h3>
            </div>
            <div class="metric-icon blue"><i class="fa-solid fa-hospital-user"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="50">
        <div class="admin-metric-card">
            <div class="metric-info">
                <h6>Đội ngũ Bác Sĩ</h6>
                <h3><?= number_format($data['totalDoctors'] ?? 0) ?></h3>
            </div>
            <div class="metric-icon green"><i class="fa-solid fa-user-doctor"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="admin-metric-card">
            <div class="metric-info">
                <h6>Lịch hẹn chờ duyệt</h6>
                <h3><?= number_format($data['pendingAppointments'] ?? 0) ?> <span style="font-size:14px;color:var(--warning)">/ <?= number_format($data['totalAppointments'] ?? 0) ?></span></h3>
            </div>
            <div class="metric-icon orange"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="150">
        <div class="admin-metric-card">
            <div class="metric-info">
                <h6>Cơ sở Thiết bị</h6>
                <h3><?= number_format($data['totalEquipment'] ?? 0) ?></h3>
            </div>
            <div class="metric-icon purple"><i class="fa-solid fa-laptop-medical"></i></div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4 mb-4">
    <!-- Chart Section -->
    <div class="col-xl-8 col-lg-7" data-aos="fade-up" data-aos-delay="200">
        <div class="chart-card" style="height: 100%; border-radius: var(--radius-lg); padding: 24px; background: white; border: 1px solid var(--gray-100); box-shadow: var(--shadow-sm);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 style="margin:0; font-weight: 600; color: var(--dark);"><i class="fa-solid fa-chart-area me-2 text-primary"></i>Lưu lượng Bệnh nhân</h6>
            </div>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="patientChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="col-xl-4 col-lg-5" data-aos="fade-up" data-aos-delay="300">
        <div class="recent-list-card">
            <div class="recent-list-header">
                <h5>Lịch hẹn mới nhất</h5>
                <a href="index.php?page=appointments" class="badge bg-primary text-decoration-none" style="border-radius: 6px; padding: 6px 12px; font-weight: 500;">Xem tất cả</a>
            </div>
            <div class="recent-list-body">
                <?php if (empty($recentAppointments)): ?>
                    <div class="text-center p-4 text-muted">Chưa có lịch hẹn nào mới</div>
                <?php else: ?>
                    <?php foreach ($recentAppointments as $app): 
                        // Map status
                        $statusClass = 'status-pending';
                        $statusText = 'Chờ xử lý';
                        if ($app['status'] == 'confirmed') { $statusClass = 'status-confirmed'; $statusText = 'Xác nhận'; }
                        if ($app['status'] == 'completed') { $statusClass = 'status-completed'; $statusText = 'Hoàn thành'; }
                        if ($app['status'] == 'cancelled') { $statusClass = 'status-cancelled'; $statusText = 'Đã hủy'; }
                    ?>
                    <div class="recent-item">
                        <div class="recent-avatar"><?= strtoupper(substr($app['patient_name'] ?? 'U', 0, 1)) ?></div>
                        <div class="recent-info">
                            <p title="<?= htmlspecialchars($app['patient_name'] ?? 'Bệnh nhân ẩn') ?>"><?= htmlspecialchars($app['patient_name'] ?? 'Bệnh nhân ẩn') ?></p>
                            <small><i class="fa-regular fa-clock me-1"></i><?= date('H:i d/m/Y', strtotime($app['appointment_date'])) ?></small>
                        </div>
                        <div>
                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-2"><h5 style="font-size: 16px; font-weight: 600; color: var(--gray-600); margin-bottom: 16px;">Lối tắt Công việc</h5></div>
<div class="row g-3">
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="0">
        <a href="index.php?page=appointments" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-calendar-plus"></i> Duyệt Lịch khám</div>
        </a>
    </div>
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="50">
        <a href="index.php?page=patients" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-hospital-user"></i> Bệnh nhân Mới</div>
        </a>
    </div>
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="100">
        <a href="index.php?page=doctors" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-user-doctor"></i> Hồ sơ Bác sĩ</div>
        </a>
    </div>
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="150">
        <a href="index.php?page=medicines" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-capsules"></i> Quản lý Thuốc</div>
        </a>
    </div>
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="200">
        <a href="index.php?page=shifts" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-user-clock"></i> Phân Ca trực</div>
        </a>
    </div>
    <div class="col-xl-2 col-md-4 col-6" data-aos="fade-up" data-aos-delay="250">
        <a href="index.php?page=consultations" class="text-decoration-none">
            <div class="quick-action-btn"><i class="fa-solid fa-video"></i> Tư vấn Online</div>
        </a>
    </div>
</div>

<!-- Chart Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initPatientChart !== 'undefined') {
        const ctx = document.getElementById('patientChart');
        if(ctx) {
            const chartData = [12, 19, 15, 25, 22, 30, 28, 35, 30, 42, 38, <?= min(100, $data['totalPatients'] ?? 50) ?>];
            
            setTimeout(() => {
                if(window.Chart) {
                    let chartInstance = Chart.getChart(ctx);
                    if(chartInstance) chartInstance.destroy(); // hủy chart cũ nếu có

                    let context2D = ctx.getContext('2d');
                    let gradient = context2D.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
                    gradient.addColorStop(1, 'rgba(14, 165, 233, 0.05)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
                            datasets: [{
                                label: 'Lượt bệnh nhân mới',
                                data: chartData,
                                borderColor: '#0ea5e9',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#0ea5e9',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4 // Làm mượt đường cong
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: { 
                                    beginAtZero: true,
                                    grid: { borderDash: [5, 5], color: '#f1f5f9' },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                }
            }, 300);
        }
    }
});
</script>
