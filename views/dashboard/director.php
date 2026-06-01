<?php
// Inject compact dashboard CSS
?>
<style>
/* ===== DIRECTOR DASHBOARD: ONE-SCREEN COMPACT LAYOUT ===== */
.dir-stat {
    background: #fff;
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    border-left: 3px solid var(--dl-color, #6366f1);
    height: 100%;
}
.dir-stat .ds-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    background: color-mix(in srgb, var(--dl-color, #6366f1) 12%, white);
    color: var(--dl-color, #6366f1);
}
.dir-stat .ds-val  { font-size: 18px; font-weight: 700; line-height: 1.1; color: #1e293b; }
.dir-stat .ds-lbl  { font-size: 10px; color: #64748b; font-weight: 500; margin-top: 1px; }

/* AI block compact */
.ai-compact-card { border-radius: 12px; border: 1px solid #c7d2fe; background: linear-gradient(135deg,#f5f3ff,#fff); }
.ai-compact-card .card-header { padding: 8px 14px; background: transparent; border-bottom: 1px solid #ede9fe; }
.ai-compact-card .card-body   { padding: 8px 14px; }

/* Charts compact */
.chart-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow:hidden; height:100%; }
.chart-card .ch-header { padding: 8px 14px; border-bottom: 1px solid #f1f5f9; }
.chart-card .ch-header h6 { margin:0; font-weight:700; font-size:13px; color:#334155; }
.chart-card .ch-body { padding: 8px 14px; }

/* Top-doctors mini table */
.mini-table { width:100%; border-collapse:collapse; font-size:12px; }
.mini-table th { color:#64748b; font-weight:600; padding:3px 6px; border-bottom:1px solid #f1f5f9; }
.mini-table td { padding:3px 6px; }
.mini-table tr:last-child td { border-bottom:none; }
</style>

<!-- ===== ROW 1: 8 Stat Cards in ONE row ===== -->
<div class="row g-2 mb-2">

    <div class="col" style="--dl-color:#6366f1">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-hospital-user"></i></div>
            <div>
                <div class="ds-val"><?= number_format($directorStats['total_patients'] ?? 0) ?></div>
                <div class="ds-lbl">Tổng Bệnh nhân</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#22c55e">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div>
                <div class="ds-val"><?= number_format(($directorStats['month_revenue'] ?? 0)/1000000, 1) ?>M</div>
                <div class="ds-lbl">Doanh thu tháng</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#f59e0b">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['today_appointments'] ?? 0 ?></div>
                <div class="ds-lbl">Lịch hẹn hôm nay</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#0ea5e9">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-bed-pulse"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['current_inpatients'] ?? 0 ?></div>
                <div class="ds-lbl">Nội trú hiện tại</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#8b5cf6">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['total_doctors'] ?? 0 ?></div>
                <div class="ds-lbl">Bác sĩ</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#ec4899">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-user-nurse"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['total_nurses'] ?? 0 ?></div>
                <div class="ds-lbl">Y tá</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#ef4444">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-capsules"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['low_stock_medicines'] ?? 0 ?></div>
                <div class="ds-lbl">Thuốc tồn kho thấp</div>
            </div>
        </div>
    </div>

    <div class="col" style="--dl-color:#d97706">
        <div class="dir-stat">
            <div class="ds-icon"><i class="fa-solid fa-flask-vial"></i></div>
            <div>
                <div class="ds-val"><?= $directorStats['pending_lab_orders'] ?? 0 ?></div>
                <div class="ds-lbl">CLS chờ xử lý</div>
            </div>
        </div>
    </div>

</div><!-- /row1 -->

<!-- ===== ROW 2: AI Block (compact, collapsible) + Bed Chart ===== -->
<div class="row g-2 mb-2">

    <!-- AI Predict - compact -->
    <div class="col-lg-8">
        <div class="ai-compact-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="color:#4f46e5; font-weight:700; font-size:13px;">
                    <i class="fa-solid fa-brain me-1"></i>Dự báo Vận hành AI 4.0
                </span>
                <button type="button" class="btn btn-sm px-3" id="btnAiPredict"
                    style="border-radius:20px; font-weight:600; background:#4f46e5; color:#fff; border:none; font-size:12px; padding:4px 12px;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Khởi chạy
                </button>
            </div>
            <div class="card-body">
                <div id="aiPredictPlaceholder" class="text-center py-2">
                    <p class="text-muted mb-0" style="font-size:12px;">
                        <i class="fa-solid fa-circle-info me-1"></i>Nhấn nút để AI phân tích lịch hẹn, công suất giường và dự báo lưu lượng tuần tới.
                    </p>
                </div>
                <div id="aiPredictLoading" class="text-center py-2" style="display:none;">
                    <i class="fa-solid fa-circle-notch fa-spin text-primary me-2"></i>
                    <small class="text-muted">AI đang xử lý...</small>
                </div>
                <div id="aiPredictResult" style="display:none;">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-white">
                                <small class="text-muted fw-semibold" style="font-size:11px;">Tải dự kiến tuần tới</small>
                                <div class="d-flex align-items-baseline gap-2 mt-1">
                                    <h4 class="fw-bold text-primary mb-0" id="aiLoadPct">0%</h4>
                                    <span class="badge" id="aiLoadRiskBadge" style="font-size:10px;">Bình thường</span>
                                </div>
                                <div class="progress mt-1" style="height:5px;">
                                    <div class="progress-bar progress-bar-striped" id="aiLoadProgressBar" style="width:0%"></div>
                                </div>
                                <div class="d-flex gap-3 mt-2">
                                    <div>
                                        <div style="font-size:10px; color:#64748b;">Ngày cao điểm</div>
                                        <div class="fw-bold" style="font-size:11px;" id="aiPeakDays">—</div>
                                    </div>
                                    <div>
                                        <div style="font-size:10px; color:#64748b;">Khoa bận nhất</div>
                                        <div class="fw-bold" style="font-size:11px;" id="aiBusiestDept">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-white h-100">
                                <small class="text-muted fw-semibold" style="font-size:11px;"><i class="fa-solid fa-chart-simple me-1"></i>Dự báo 7 ngày tới</small>
                                <div class="table-responsive mt-1">
                                    <table class="table table-sm table-borderless align-middle mb-0" style="font-size:11px;">
                                        <tbody id="aiForecastTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-white h-100">
                                <div class="mb-2">
                                    <small class="fw-bold text-dark" style="font-size:11px;"><i class="fa-solid fa-magnifying-glass-chart me-1"></i>Phân tích xu hướng</small>
                                    <p class="text-muted lh-sm mb-0 mt-1" id="aiAnalysisText" style="font-size:11px; text-align:justify;"></p>
                                </div>
                                <div>
                                    <small class="fw-bold text-success" style="font-size:11px;"><i class="fa-solid fa-lightbulb me-1"></i>Đề xuất AI</small>
                                    <p class="text-muted lh-sm mb-0 mt-1" id="aiRecommendationsText" style="font-size:11px; text-align:justify;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bed Occupancy -->
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="ch-header"><h6><i class="fa-solid fa-bed me-1 text-primary"></i>Công suất giường</h6></div>
            <div class="ch-body text-center">
                <canvas id="bedChart" height="130"></canvas>
                <div class="mt-1">
                    <span class="badge bg-primary"><?= $bedOccupancy['occupancy_rate'] ?? 0 ?>%</span>
                    <small class="text-muted ms-2"><?= $bedOccupancy['occupied_beds'] ?? 0 ?> / <?= $bedOccupancy['total_beds'] ?? 0 ?> giường</small>
                </div>
            </div>
        </div>
    </div>

</div><!-- /row2 -->

<!-- ===== ROW 3: Revenue Chart + Top Doctors + Appointments ===== -->
<div class="row g-2">

    <!-- Revenue Bar Chart -->
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="ch-header"><h6><i class="fa-solid fa-chart-line me-1 text-primary"></i>Doanh thu theo tháng</h6></div>
            <div class="ch-body"><canvas id="revenueChart" height="150"></canvas></div>
        </div>
    </div>

    <!-- Top Bác sĩ -->
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="ch-header"><h6><i class="fa-solid fa-trophy me-1 text-warning"></i>Top Bác sĩ (30 ngày)</h6></div>
            <div class="ch-body">
                <?php if (!empty($topDoctors)): ?>
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Bác sĩ</th><th>Chuyên khoa</th><th style="text-align:center">Lượt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topDoctors as $idx => $doc): ?>
                        <tr>
                            <td>
                                <?php if ($idx === 0): ?><span>🥇</span>
                                <?php elseif ($idx === 1): ?><span>🥈</span>
                                <?php elseif ($idx === 2): ?><span>🥉</span>
                                <?php else: ?><?= $idx + 1 ?><?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($doc['name']) ?></strong></td>
                            <td><span class="text-muted"><?= htmlspecialchars($doc['specialty'] ?? '') ?></span></td>
                            <td style="text-align:center"><span class="badge bg-primary" style="font-size:11px;"><?= $doc['record_count'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-muted text-center py-3" style="font-size:12px;">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Appointment Doughnut -->
    <div class="col-lg-3">
        <div class="chart-card h-100">
            <div class="ch-header"><h6><i class="fa-solid fa-chart-pie me-1 text-primary"></i>Lịch hẹn tháng này</h6></div>
            <div class="ch-body text-center"><canvas id="appointmentChart" height="170"></canvas></div>
        </div>
    </div>

</div><!-- /row3 -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ---- AI Predict ----
    const btnAi = document.getElementById('btnAiPredict');
    btnAi.addEventListener('click', async function() {
        document.getElementById('aiPredictPlaceholder').style.display = 'none';
        document.getElementById('aiPredictLoading').style.display = 'block';
        document.getElementById('aiPredictResult').style.display = 'none';
        btnAi.disabled = true;

        try {
            const res  = await fetch('index.php?page=dashboard&action=aiPredictLoad');
            const data = await res.json();

            if (data.success && data.data) {
                const d = data.data;
                document.getElementById('aiLoadPct').textContent      = d.predicted_load_percentage + '%';
                document.getElementById('aiPeakDays').textContent     = d.peak_days.join(', ');
                document.getElementById('aiBusiestDept').textContent  = d.busiest_department;
                document.getElementById('aiAnalysisText').textContent       = d.analysis;
                document.getElementById('aiRecommendationsText').textContent = d.recommendations;

                const pb = document.getElementById('aiLoadProgressBar');
                pb.style.width = d.predicted_load_percentage + '%';
                pb.className   = 'progress-bar progress-bar-striped ' +
                    (d.predicted_load_percentage > 85 ? 'bg-danger' :
                     d.predicted_load_percentage > 60 ? 'bg-warning' : 'bg-success');

                const riskMap = {
                    normal: { text:'Bình thường', bg:'#22c55e' },
                    warning:{ text:'Cảnh báo',    bg:'#f59e0b' },
                    danger: { text:'Quá tải ⚠️',  bg:'#ef4444' },
                    critical:{ text:'Nguy cấp 🚨', bg:'#dc2626' }
                };
                const risk = riskMap[d.risk_level] || { text: d.risk_level, bg:'#94a3b8' };
                const rb = document.getElementById('aiLoadRiskBadge');
                rb.textContent = risk.text; rb.style.backgroundColor = risk.bg;

                const statusBadges = {
                    low:   { text:'Thấp', bg:'#e0f2fe', color:'#0369a1' },
                    medium:{ text:'TB',   bg:'#fef3c7', color:'#b45309' },
                    high:  { text:'Cao ⚠️', bg:'#fee2e2', color:'#b91c1c' }
                };
                const tbody = document.getElementById('aiForecastTableBody');
                tbody.innerHTML = '';
                d.forecast_7days.forEach(f => {
                    const sb = statusBadges[f.load_status] || { text: f.load_status, bg:'#f3f4f6', color:'#374151' };
                    tbody.innerHTML += `<tr>
                        <td class="fw-semibold" style="width:30%;font-size:11px">${f.day}</td>
                        <td style="width:40%"><span class="badge bg-light text-dark border" style="font-size:10px">${f.estimated_patients} BN</span></td>
                        <td><span class="badge" style="background:${sb.bg};color:${sb.color};font-size:10px">${sb.text}</span></td>
                    </tr>`;
                });

                document.getElementById('aiPredictResult').style.display = 'block';
            } else {
                alert('Dự báo thất bại: ' + (data.error || 'Lỗi không rõ'));
                document.getElementById('aiPredictPlaceholder').style.display = 'block';
            }
        } catch(err) {
            alert('Lỗi kết nối máy chủ AI.');
            document.getElementById('aiPredictPlaceholder').style.display = 'block';
        }

        document.getElementById('aiPredictLoading').style.display = 'none';
        btnAi.disabled = false;
    });

    // ---- Revenue Chart ----
    const revData   = <?= json_encode($revenueByMonth ?? []) ?>;
    const revLabels = revData.map(r => r.month);
    const revValues = revData.map(r => r.revenue);
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revLabels,
            datasets: [{ label:'Doanh thu (VNĐ)', data: revValues,
                backgroundColor:'rgba(99,102,241,0.15)', borderColor:'#6366f1',
                borderWidth:2, borderRadius:6, hoverBackgroundColor:'rgba(99,102,241,0.3)' }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display:false } },
            scales: {
                y: { beginAtZero:true, ticks:{ font:{size:10}, callback: v => (v/1000000).toFixed(1)+'M' } },
                x: { grid: { display:false }, ticks:{ font:{size:10} } }
            }
        }
    });

    // ---- Bed Chart ----
    const bedOcc = <?= json_encode($bedOccupancy ?? ['occupied_beds'=>0,'total_beds'=>1]) ?>;
    const freeB  = Math.max(0, bedOcc.total_beds - bedOcc.occupied_beds);
    new Chart(document.getElementById('bedChart'), {
        type: 'doughnut',
        data: { labels:['Đang sử dụng','Trống'],
            datasets:[{ data:[bedOcc.occupied_beds, freeB],
                backgroundColor:['#6366f1','#e5e7eb'], borderWidth:0, cutout:'72%' }]
        },
        options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11}, boxWidth:10 } } } }
    });

    // ---- Appointment Chart ----
    const apptData    = <?= json_encode($appointmentsByStatus ?? []) ?>;
    const statusLabels = { pending:'Chờ XN', confirmed:'Đã XN', completed:'Hoàn thành', cancelled:'Đã hủy', emergency:'Cấp cứu' };
    const statusColors = { pending:'#f59e0b', confirmed:'#3b82f6', completed:'#22c55e', cancelled:'#ef4444', emergency:'#dc2626' };
    new Chart(document.getElementById('appointmentChart'), {
        type: 'doughnut',
        data: { labels: apptData.map(a => statusLabels[a.status] || a.status),
            datasets:[{ data: apptData.map(a => a.total),
                backgroundColor: apptData.map(a => statusColors[a.status] || '#94a3b8'), borderWidth:0 }]
        },
        options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:10}, boxWidth:10 } } } }
    });
});
</script>
