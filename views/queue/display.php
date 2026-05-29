<!DOCTYPE html>
<html lang="vi" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Màn hình Gọi số - NovaCare Smart Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --primary: #38bdf8;
            --success: #34d399;
            --warning: #fbbf24;
            --emergency: #f87171;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: radial-gradient(circle at 10% 20%, rgba(56, 189, 248, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(52, 211, 153, 0.05) 0%, transparent 40%);
        }

        /* Header bar */
        .board-header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hospital-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hospital-logo i {
            font-size: 2rem;
            color: var(--primary);
        }

        .hospital-logo h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .hospital-logo span {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .clock-display {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            font-variant-numeric: tabular-nums;
        }

        /* Layout Grid */
        .board-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 25px;
            padding: 25px;
            height: calc(100vh - 85px);
        }

        /* Large Current Called Section */
        .current-called-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100%;
        }

        .calling-now-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
        }

        .calling-now-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--success));
        }

        .calling-label {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .calling-number {
            font-size: 8rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 0 0 40px rgba(56, 189, 248, 0.4);
            font-variant-numeric: tabular-nums;
        }

        .calling-patient {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-light);
        }

        .calling-destination {
            font-size: 2rem;
            color: var(--success);
            font-weight: 600;
            background: rgba(52, 211, 153, 0.1);
            padding: 10px 30px;
            border-radius: 50px;
            border: 1px solid rgba(52, 211, 153, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        /* List Column */
        .waiting-column {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            height: 100%;
        }

        .waiting-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-light);
        }

        .waiting-list-container {
            flex: 1;
            overflow-y: auto;
        }

        .waiting-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            margin-bottom: 12px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .waiting-row:hover {
            background: rgba(15, 23, 42, 0.6);
            border-color: rgba(56, 189, 248, 0.2);
        }

        .waiting-row.emergency {
            border-left: 5px solid var(--emergency);
            background: rgba(248, 113, 113, 0.05);
        }

        .waiting-row.priority {
            border-left: 5px solid var(--warning);
            background: rgba(251, 191, 36, 0.05);
        }

        .waiting-row-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            font-variant-numeric: tabular-nums;
        }

        .waiting-row-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .waiting-row-dept {
            font-size: 1.1rem;
            color: var(--text-muted);
            text-align: right;
        }

        .waiting-row-room {
            font-size: 0.9rem;
            color: var(--success);
            font-weight: 600;
        }

        /* Footer bar */
        .board-footer {
            background: rgba(15, 23, 42, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 30px;
            font-size: 1.1rem;
            color: var(--text-muted);
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Voice/Audio call icon trigger */
        .sound-indicator {
            cursor: pointer;
            color: var(--primary);
        }

        .sound-indicator.muted {
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <header class="board-header">
        <div class="hospital-logo">
            <i class="fa-solid fa-hospital"></i>
            <div>
                <h1>NovaCare</h1>
                <span>Smart Hospital Dashboard</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div id="sound-btn" class="sound-indicator" title="Mute/Unmute giọng đọc">
                <i class="fa-solid fa-volume-high fs-3"></i>
            </div>
            <div class="clock-display" id="clock">00:00:00</div>
        </div>
    </header>

    <main class="board-content">
        <!-- Left: Current Called Ticket -->
        <section class="current-called-column">
            <div class="calling-now-card" id="current-called-card">
                <div class="calling-label">Đang gọi khám bệnh</div>
                <div class="calling-number" id="current-number">----</div>
                <div class="calling-patient" id="current-patient">Chưa có bệnh nhân</div>
                <div class="calling-destination" id="current-destination">
                    <i class="fa-solid fa-door-open"></i>
                    <span id="current-room">---</span>
                </div>
            </div>
        </section>

        <!-- Right: Queue List -->
        <section class="waiting-column">
            <div class="waiting-title">
                <span><i class="fa-solid fa-users me-2"></i>Danh sách chờ tiếp theo</span>
                <span class="badge bg-primary" id="waiting-count">0</span>
            </div>
            <div class="waiting-list-container" id="waiting-list">
                <!-- Rows populated by Javascript -->
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x mb-3"></i>
                    <p>Đang tải danh sách hàng chờ...</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="board-footer">
        <div>Xin vui lòng chuẩn bị sẵn thẻ BHYT và CMND/CCCD khi được gọi số.</div>
        <div class="fw-bold text-success">NovaCare Smart Queue Board v4.0</div>
    </footer>

    <!-- Audio chime on status update -->
    <audio id="chime" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-84.wav" preload="auto"></audio>

    <script>
        let lastCalledId = null;
        let isMuted = false;

        // Clock logic
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toTimeString().split(' ')[0];
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Sound trigger
        document.getElementById('sound-btn').addEventListener('click', function() {
            isMuted = !isMuted;
            const icon = this.querySelector('i');
            if (isMuted) {
                icon.className = 'fa-solid fa-volume-xmark fs-3 text-muted';
                this.title = "Bật âm thanh";
            } else {
                icon.className = 'fa-solid fa-volume-high fs-3 text-primary';
                this.title = "Tắt âm thanh";
            }
        });

        // Speech Synthesis for Vietnam Text
        function speakTicket(number, name, room) {
            if (isMuted) return;

            // Simple chime play
            const chime = document.getElementById('chime');
            chime.play().catch(() => {});

            // Voice Text-to-Speech
            setTimeout(() => {
                if ('speechSynthesis' in window) {
                    const text = `Xin mời bệnh nhân ${name}, số thứ tự ${number}, đến ${room}`;
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'vi-VN';
                    utterance.rate = 0.9;
                    window.speechSynthesis.speak(utterance);
                }
            }, 1000);
        }

        // Pad number utility
        function pad(num, size) {
            let s = num + "";
            while (s.length < size) s = "0" + s;
            return s;
        }

        // Render Queue UI with received data
        function updateQueueUI(data) {
            // Update current called ticket
            const current = data.called && data.called.length > 0 ? data.called[0] : null;
            const numberEl = document.getElementById('current-number');
            const patientEl = document.getElementById('current-patient');
            const roomEl = document.getElementById('current-room');
            const cardEl = document.getElementById('current-called-card');

            if (current) {
                const paddedNum = pad(current.ticket_number, 4);
                const roomName = current.room_name || 'Phòng khám ' + (current.department_name || 'Đa khoa');
                
                numberEl.textContent = paddedNum;
                patientEl.textContent = current.patient_name;
                roomEl.textContent = roomName;

                // Trigger Voice & Sound if it's a new call
                if (current.id !== lastCalledId) {
                    lastCalledId = current.id;
                    speakTicket(paddedNum, current.patient_name, roomName);
                    
                    // Flashing animation
                    cardEl.classList.remove('animate__animated', 'animate__pulse');
                    void cardEl.offsetWidth; // Trigger reflow
                    cardEl.classList.add('animate__animated', 'animate__pulse');
                }
            } else {
                numberEl.textContent = "----";
                patientEl.textContent = "Chưa có bệnh nhân";
                roomEl.textContent = "---";
                lastCalledId = null;
            }

            // Update waiting list
            const waitingListEl = document.getElementById('waiting-list');
            const waitingCountEl = document.getElementById('waiting-count');
            
            waitingCountEl.textContent = data.stats ? data.stats.waiting : 0;

            if (!data.waiting || data.waiting.length === 0) {
                waitingListEl.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-face-smile fa-3x mb-3 text-primary"></i>
                        <p class="fs-5">Không có bệnh nhân chờ khám</p>
                    </div>
                `;
            } else {
                let html = '';
                data.waiting.forEach(w => {
                    const prioClass = w.priority === 'emergency' ? 'emergency' : (w.priority === 'priority' ? 'priority' : '');
                    const prioBadge = w.priority === 'emergency' ? '<span class="badge bg-danger ms-2">CẤP CỨU</span>' : (w.priority === 'priority' ? '<span class="badge bg-warning text-dark ms-2">ƯU TIÊN</span>' : '');
                    const destText = w.room_name || w.department_name || 'Đa khoa';

                    html += `
                        <div class="waiting-row ${prioClass}">
                            <div>
                                <span class="waiting-row-number">${pad(w.ticket_number, 4)}</span>
                                ${prioBadge}
                                <div class="waiting-row-name">${w.patient_name}</div>
                            </div>
                            <div class="waiting-row-dept">
                                <div>${destText}</div>
                                <div class="waiting-row-room"><i class="fa-solid fa-circle-info me-1"></i>Chờ gọi</div>
                            </div>
                        </div>
                    `;
                });
                waitingListEl.innerHTML = html;
            }
        }

        // Fetch Data from JSON API (Fallback mode)
        async function fetchQueueData() {
            try {
                const response = await fetch('index.php?page=queue&action=displayData');
                if (!response.ok) throw new Error('Network response error');
                const data = await response.json();
                updateQueueUI(data);
            } catch (err) {
                console.error("Lỗi lấy dữ liệu hàng chờ (Polling): ", err);
            }
        }

        // ====== Server-Sent Events (SSE) Client 4.0 ======
        let eventSource = null;
        let pollingInterval = null;

        function connectSSE() {
            if (!!window.EventSource) {
                console.log("Khởi tạo kết nối SSE Realtime...");
                eventSource = new EventSource('index.php?page=queue&action=streamQueue');

                eventSource.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        console.log("Nhận cập nhật gọi số (SSE):", data.timestamp);
                        updateQueueUI(data);
                    } catch (e) {
                        console.error("Lỗi parse SSE JSON:", e);
                    }
                };

                eventSource.onerror = function(err) {
                    console.warn("SSE gặp sự cố hoặc ngắt kết nối. Đang kích hoạt Polling dự phòng...");
                    eventSource.close();
                    startFallbackPolling();
                };
            } else {
                console.log("Trình duyệt không hỗ trợ SSE. Dùng Polling truyền thống.");
                startFallbackPolling();
            }
        }

        function startFallbackPolling() {
            if (pollingInterval) return;
            fetchQueueData();
            pollingInterval = setInterval(fetchQueueData, 5000);
        }

        // Bắt đầu chạy
        connectSSE();
    </script>
</body>
</html>
