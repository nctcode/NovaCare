<!-- views/consultations/room.php -->
<div class="content-card mb-4" data-aos="fade-up">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fa-solid fa-video me-2"></i>Phòng Tư vấn: <?= htmlspecialchars($meeting['meeting_id']) ?></h5>
        <a href="index.php?page=consultations" class="btn btn-outline-danger btn-sm" id="btn-leave-room">
            <i class="fa-solid fa-phone-slash me-1"></i> Rời phòng
        </a>
    </div>
    <div class="card-body p-0">
        <!-- Container for Jitsi iframe -->
        <div id="jitsi-container" style="width: 100%; height: 75vh; background-color: #111;">
            <div id="jitsi-loading" class="text-center text-white pt-5">
                <i class="fa-solid fa-circle-notch fa-spin fa-3x mb-3"></i>
                <p>Đang kết nối đến máy chủ Jitsi Meet...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const domain = 'meet.jit.si';
    const options = {
        roomName: 'NovaCare_<?= htmlspecialchars($meeting['meeting_id']) ?>',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: '<?= htmlspecialchars($user['name']) ?>'
        },
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            prejoinPageEnabled: false, // Skip prejoin page to enter directly
            disableDeepLinking: true // Prevent Jitsi app prompt on mobile
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            DEFAULT_BACKGROUND: '#111111',
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat',
                'settings', 'videoquality', 'filmstrip', 'tileview'
            ]
        }
    };
    
    // Hide loading after a slight delay assuming init is fast
    setTimeout(() => {
        const loader = document.getElementById('jitsi-loading');
        if (loader) loader.style.display = 'none';
    }, 1500);

    const api = new JitsiMeetExternalAPI(domain, options);
    
    // Xử lý sự kiện khi user tự bấm nút đỏ "Rời phòng" của Jitsi
    api.addEventListener('videoConferenceLeft', function() {
        window.location.href = 'index.php?page=consultations';
    });
});
</script>
