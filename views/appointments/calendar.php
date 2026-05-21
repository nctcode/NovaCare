<!-- FullCalendar Appointments View -->

<!-- Include FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
    <div>
        <h5 class="m-0"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Lịch Khám Bệnh Trực Quan</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý lịch hẹn theo ngày, tuần, tháng</p>
    </div>
    <a href="index.php?page=appointments" class="btn btn-outline-primary" style="border-radius:20px; font-weight:500;">
        <i class="fa-solid fa-list me-2"></i>Dạng Danh sách
    </a>
</div>

<div class="row" data-aos="fade-up" data-aos-delay="100">
    <div class="col-lg-3 mb-4 mb-lg-0">
        <div class="content-card mb-4" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h6 style="font-weight:700;"><i class="fa-solid fa-circle-info me-2 text-info"></i>Chú thích Trạng thái</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="d-inline-block rounded-circle me-3" style="width:14px; height:14px; background:#f59e0b;"></span>
                    <span class="text-dark" style="font-weight:500;">Chờ xác nhận</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="d-inline-block rounded-circle me-3" style="width:14px; height:14px; background:#10b981;"></span>
                    <span class="text-dark" style="font-weight:500;">Đã xác nhận</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="d-inline-block rounded-circle me-3" style="width:14px; height:14px; background:#64748b;"></span>
                    <span class="text-dark" style="font-weight:500;">Hoàn thành</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded-circle me-3" style="width:14px; height:14px; background:#ef4444;"></span>
                    <span class="text-dark" style="font-weight:500;">Đã hủy</span>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25" style="border-radius:12px;">
            <i class="fa-solid fa-lightbulb me-2 text-info"></i>Bấm vào một sự kiện trên lịch để chuyển đến trang quản lý danh sách và cập nhật trạng thái.
        </div>
    </div>
    
    <div class="col-lg-9">
        <div class="content-card" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03); padding:20px;">
            <!-- Calendar Container -->
            <div id="calendar"></div>
        </div>
    </div>
</div>

<style>
/* Custom FullCalendar styling over Bootstrap */
.fc-theme-standard td, .fc-theme-standard th { border-color: var(--gray-200); }
.fc-header-toolbar { margin-bottom: 1.5rem !important; }
.fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700 !important; color: var(--dark); text-transform: capitalize; }
.fc-button-primary { background-color: var(--primary) !important; border-color: var(--primary) !important; border-radius: 8px !important; text-transform: capitalize;}
.fc-button-primary:hover { background-color: #0c8ec9 !important; }
.fc-button-active { background-color: #0c8ec9 !important; border-color: #0c8ec9 !important; }
.fc-event { border-radius: 4px; padding: 2px 4px; border: none; font-size: 12px; font-weight: 500; cursor: pointer; transition: transform 0.2s;}
.fc-event:hover { transform: scale(1.02); }
.fc-day-today { background-color: rgba(14, 165, 233, 0.05) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'vi', // Vietnamese
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hôm nay',
            month: 'Tháng',
            week: 'Tuần',
            day: 'Ngày',
            list: 'Danh sách'
        },
        firstDay: 1, // Start week on Monday
        slotMinTime: '07:00:00', // Typical hospital hours
        slotMaxTime: '19:00:00',
        events: <?= $eventsJson ?>,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        }
    });
    calendar.render();
});
</script>
