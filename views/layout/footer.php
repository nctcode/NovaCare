    </div><!-- /page-content -->
</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="assets/js/dashboard.js"></script>

<script>
// Auto dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            new bootstrap.Alert(alert).close();
        });
    }, 5000);
});

// Confirm delete
function confirmDelete(url) {
    if (confirm('Bạn có chắc chắn muốn xóa?')) {
        window.location.href = url;
    }
}
</script>
</body>
</html>
