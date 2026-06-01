<?php
/**
 * views/queue/my_ticket.php
 * Trang theo dõi số thứ tự khám bệnh của Bệnh nhân - Real-time
 */
?>

<style>
    .queue-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #0e4d92 100%);
        border-radius: 24px;
        padding: 48px 40px;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(14, 77, 146, 0.35);
    }
    .queue-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(56,189,248,0.18) 0%, transparent 70%);
        border-radius: 50%;
    }
    .queue-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: -40px;
        width: 160px; height: 160px;
        background: radial-gradient(circle, rgba(52,211,153,0.12) 0%, transparent 70%);
        border-radius: 50%;
    }

    .ticket-number-display {
        font-size: 7rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -2px;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 0 40px rgba(56,189,248,0.5);
        color: #38bdf8;
    }
    .ticket-label {
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 12px;
    }
    .ticket-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        margin-top: 16px;
    }
    .status-waiting  { background: rgba(251,191,36,0.15);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
    .status-called   { background: rgba(56,189,248,0.15);  color: #38bdf8; border: 1px solid rgba(56,189,248,0.3);  animation: pulse-glow 1.2s ease-in-out infinite; }
    .status-in_progress { background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.3); animation: pulse-glow 1.2s ease-in-out infinite; }
    .status-completed { background: rgba(100,116,139,0.15); color: #94a3b8; border: 1px solid rgba(100,116,139,0.3); }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(56,189,248,0.4); }
        50%       { box-shadow: 0 0 0 10px rgba(56,189,248,0); }
    }

    .ahead-count-card {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 20px 30px;
        display: inline-block;
        margin-top: 24px;
        backdrop-filter: blur(10px);
    }
    .ahead-count-number {
        font-size: 2.8rem;
        font-weight: 900;
        color: #fbbf24;
    }
    .ahead-count-label {
        font-size: 0.85rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-top: 32px;
    }
    .info-item {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px;
        padding: 16px;
        text-align: center;
    }
    .info-item-icon { font-size: 1.5rem; margin-bottom: 8px; }
    .info-item-value { font-size: 1rem; font-weight: 700; color: #f8fafc; }
    .info-item-label { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

    .no-ticket-card {
        text-align: center;
        padding: 60px 30px;
        color: #64748b;
    }
    .no-ticket-card i { font-size: 4rem; margin-bottom: 20px; color: #94a3b8; }

    .called-now-section {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .called-now-number {
        font-size: 3.5rem;
        font-weight: 900;
        color: #0ea5e9;
        font-variant-numeric: tabular-nums;
    }

    .realtime-dot {
        display: inline-block;
        width: 8px; height: 8px;
        background: #34d399;
        border-radius: 50%;
        margin-right: 6px;
        animation: blink 1.2s ease-in-out infinite;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.2; }
    }

    .alert-called {
        background: linear-gradient(135deg, #0ea5e9, #38bdf8);
        color: #fff;
        border-radius: 16px;
        padding: 20px 28px;
        font-size: 1.1rem;
        font-weight: 600;
        animation: slide-in 0.5s ease;
    }
    @keyframes slide-in {
        from { transform: translateY(-12px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold"><i class="fa-solid fa-ticket me-2 text-primary"></i>Số thứ tự của tôi</h4>
            <p class="text-muted mb-0" style="font-size:14px;">
                <span class="realtime-dot"></span>Tự động cập nhật mỗi 5 giây
                <span class="ms-2 text-muted" id="last-updated"></span>
            </p>
        </div>
        <a href="index.php?page=dashboard" class="btn btn-outline-secondary" style="border-radius:20px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row g-4">
        <!-- Left: My Ticket -->
        <div class="col-lg-7">
            <div id="ticket-area">
                <?php if ($ticket): ?>
                    <?php
                        $statusLabels = [
                            'waiting'     => ['label' => 'Đang chờ',    'icon' => 'fa-hourglass-half',  'class' => 'status-waiting'],
                            'called'      => ['label' => 'Đang được gọi!','icon' => 'fa-bell',           'class' => 'status-called'],
                            'in_progress' => ['label' => 'Đang khám',   'icon' => 'fa-stethoscope',     'class' => 'status-in_progress'],
                        ];
                        $s = $statusLabels[$ticket['status']] ?? ['label' => ucfirst($ticket['status']), 'icon' => 'fa-circle-info', 'class' => 'status-waiting'];
                        $paddedNum = str_pad($ticket['ticket_number'], 4, '0', STR_PAD_LEFT);
                    ?>

                    <?php if (in_array($ticket['status'], ['called', 'in_progress'])): ?>
                    <div class="alert-called mb-3">
                        <i class="fa-solid fa-bell-ring me-2"></i>
                        ĐẾN LƯỢT CỦA BẠN! Vui lòng đến
                        <strong><?= htmlspecialchars($ticket['room_name'] ?? 'phòng khám') ?></strong> ngay bây giờ.
                    </div>
                    <?php endif; ?>

                    <div class="queue-hero mb-4">
                        <div class="ticket-label">Số thứ tự của bạn</div>
                        <div class="ticket-number-display" id="my-ticket-number"><?= $paddedNum ?></div>

                        <div class="ticket-status-badge <?= $s['class'] ?>" id="my-status-badge">
                            <i class="fa-solid <?= $s['icon'] ?>"></i>
                            <span id="my-status-text"><?= $s['label'] ?></span>
                        </div>

                        <?php if ($ticket['status'] === 'waiting'): ?>
                        <div class="ahead-count-card" id="ahead-block">
                            <div class="ahead-count-number" id="ahead-count"><?= $aheadCount ?></div>
                            <div class="ahead-count-label">người đứng trước bạn</div>
                        </div>
                        <?php endif; ?>

                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-item-icon">🏥</div>
                                <div class="info-item-value"><?= htmlspecialchars($ticket['department_name'] ?? '---') ?></div>
                                <div class="info-item-label">Khoa khám</div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-icon">👨‍⚕️</div>
                                <div class="info-item-value"><?= $ticket['doctor_name'] ? 'BS. '.htmlspecialchars($ticket['doctor_name']) : 'Chưa phân công' ?></div>
                                <div class="info-item-label">Bác sĩ</div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-icon">🚪</div>
                                <div class="info-item-value"><?= htmlspecialchars($ticket['room_name'] ?? '---') ?></div>
                                <div class="info-item-label">Phòng khám</div>
                            </div>
                            <div class="info-item">
                                <div class="info-item-icon">⭐</div>
                                <div class="info-item-value">
                                    <?php
                                        $prioMap = ['emergency' => 'Cấp cứu', 'priority' => 'Ưu tiên', 'normal' => 'Thường'];
                                        echo $prioMap[$ticket['priority']] ?? 'Thường';
                                    ?>
                                </div>
                                <div class="info-item-label">Ưu tiên</div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="queue-hero">
                        <div class="no-ticket-card">
                            <i class="fa-solid fa-ticket-simple"></i>
                            <h5 class="fw-bold text-white mb-2">Bạn chưa có số thứ tự hôm nay</h5>
                            <p style="color:#94a3b8; font-size:14px;">
                                Khi Lễ tân cấp số cho bạn, thông tin sẽ tự động hiển thị tại đây.
                            </p>
                            <a href="index.php?page=appointments&action=create"
                               class="btn btn-primary mt-2" style="border-radius:20px;">
                                <i class="fa-solid fa-calendar-plus me-2"></i>Đặt lịch khám
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Đang được gọi hiện tại -->
        <div class="col-lg-5">
            <div class="called-now-section mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="fa-solid fa-volume-high text-primary me-2"></i>Số đang được gọi
                </h6>
                <div id="called-list">
                    <?php if (!empty($calledTickets)): ?>
                        <?php foreach ($calledTickets as $c): ?>
                        <div class="d-flex align-items-center justify-content-between p-3 mb-2 rounded-3"
                             style="background:#f0f9ff; border:1px solid #bae6fd;">
                            <div>
                                <div class="called-now-number"><?= str_pad($c['ticket_number'],4,'0',STR_PAD_LEFT) ?></div>
                                <div style="font-size:13px; color:#0369a1;">
                                    <i class="fa-solid fa-door-open me-1"></i>
                                    <?= htmlspecialchars($c['room_name'] ?? $c['department_name'] ?? 'Phòng khám') ?>
                                </div>
                            </div>
                            <div class="badge bg-primary rounded-pill px-3 py-2">Đang gọi</div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-circle-pause fa-2x mb-2"></i>
                            <p class="mb-0" style="font-size:14px;">Chưa có số nào đang được gọi</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card border-0" style="border-radius:16px; background:#fffbeb;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-info text-warning me-2"></i>Lưu ý</h6>
                    <ul class="mb-0 ps-3" style="font-size:13px; color:#78350f; line-height:1.9;">
                        <li>Vui lòng <strong>không rời khỏi khu vực chờ</strong> khi đến lượt gần.</li>
                        <li>Chuẩn bị sẵn <strong>CCCD/CMND</strong> và thẻ <strong>BHYT</strong> khi được gọi.</li>
                        <li>Nếu bỏ lỡ lượt gọi, vui lòng liên hệ <strong>quầy Lễ tân</strong> để được hỗ trợ.</li>
                        <li>Trang này <strong>tự cập nhật</strong> — không cần tải lại trang.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const STATUS_MAP = {
        waiting:     { label: 'Đang chờ',      icon: 'fa-hourglass-half', cls: 'status-waiting' },
        called:      { label: 'Đang được gọi!', icon: 'fa-bell',           cls: 'status-called' },
        in_progress: { label: 'Đang khám',      icon: 'fa-stethoscope',    cls: 'status-in_progress' },
        completed:   { label: 'Đã khám xong',   icon: 'fa-circle-check',   cls: 'status-completed' },
    };

    function pad(n, size = 4) {
        let s = String(n);
        while (s.length < size) s = '0' + s;
        return s;
    }

    function renderCalledList(calledTickets) {
        const el = document.getElementById('called-list');
        if (!calledTickets || calledTickets.length === 0) {
            el.innerHTML = `<div class="text-center py-4 text-muted">
                <i class="fa-solid fa-circle-pause fa-2x mb-2"></i>
                <p class="mb-0" style="font-size:14px;">Chưa có số nào đang được gọi</p>
            </div>`;
            return;
        }
        el.innerHTML = calledTickets.map(c => `
            <div class="d-flex align-items-center justify-content-between p-3 mb-2 rounded-3"
                 style="background:#f0f9ff; border:1px solid #bae6fd;">
                <div>
                    <div class="called-now-number">${pad(c.ticket_number)}</div>
                    <div style="font-size:13px; color:#0369a1;">
                        <i class="fa-solid fa-door-open me-1"></i>
                        ${c.room_name || c.department_name || 'Phòng khám'}
                    </div>
                </div>
                <div class="badge bg-primary rounded-pill px-3 py-2">Đang gọi</div>
            </div>
        `).join('');
    }

    function updateMyTicket(data) {
        const ticket = data.ticket;
        const aheadCount = data.ahead_count ?? 0;

        // Update called list always
        renderCalledList(data.called);

        // Update last updated time
        document.getElementById('last-updated').textContent = 'Cập nhật lúc ' + data.timestamp;

        if (!ticket) return; // No ticket, no update needed

        const numEl    = document.getElementById('my-ticket-number');
        const badgeEl  = document.getElementById('my-status-badge');
        const textEl   = document.getElementById('my-status-text');
        const aheadEl  = document.getElementById('ahead-count');
        const aheadBlk = document.getElementById('ahead-block');

        if (!numEl) return;

        const s = STATUS_MAP[ticket.status] || { label: ticket.status, icon: 'fa-circle-info', cls: 'status-waiting' };

        numEl.textContent = pad(ticket.ticket_number);
        textEl.textContent = s.label;

        // Update status badge class
        badgeEl.className = 'ticket-status-badge ' + s.cls;
        badgeEl.querySelector('i').className = 'fa-solid ' + s.icon;

        // Update ahead count
        if (aheadEl && aheadBlk) {
            if (ticket.status === 'waiting') {
                aheadEl.textContent = aheadCount;
                aheadBlk.style.display = 'inline-block';
            } else {
                aheadBlk.style.display = 'none';
            }
        }

        // Alert if called - play chime + browser notification
        if (ticket.status === 'called' || ticket.status === 'in_progress') {
            if (!document.querySelector('.alert-called')) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert-called mb-3';
                alertDiv.innerHTML = `<i class="fa-solid fa-bell-ring me-2"></i>
                    ĐẾN LƯỢT CỦA BẠN! Vui lòng đến
                    <strong>${ticket.room_name || 'phòng khám'}</strong> ngay bây giờ.`;
                document.getElementById('ticket-area').prepend(alertDiv);

                // Browser Notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('🔔 NovaCare - Đến lượt của bạn!', {
                        body: `Số ${pad(ticket.ticket_number)} - Vui lòng đến ${ticket.room_name || 'phòng khám'}.`,
                        icon: '/favicon.ico'
                    });
                }
            }
        }
    }

    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Polling every 5 seconds
    async function poll() {
        try {
            const res = await fetch('index.php?page=queue&action=myTicketData');
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
            updateMyTicket(data);
        } catch (e) {
            console.warn('Lỗi cập nhật hàng chờ:', e);
        }
    }

    // Initial poll after 2s, then every 5s
    setTimeout(poll, 2000);
    setInterval(poll, 5000);
</script>
