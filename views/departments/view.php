<!-- Chi tiết Khoa -->
<div class="row g-4" data-aos="fade-up">
    <div class="col-12">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-building-columns me-2"></i><?= htmlspecialchars($department['name']) ?></h5>
                <a href="index.php?page=departments" class="btn-action btn-edit"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
            </div>
            <div class="card-body">
                <p style="color:var(--gray-600);margin-bottom:20px;"><?= htmlspecialchars($department['description'] ?? 'Không có mô tả') ?></p>
            </div>
        </div>
    </div>

    <!-- Bác sĩ -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-user-doctor me-2"></i>Bác sĩ (<?= count($doctors) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($doctors)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Họ tên</th><th>Email</th></tr></thead>
                        <tbody>
                            <?php foreach ($doctors as $doc): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($doc['name']) ?></strong></td>
                                <td><?= htmlspecialchars($doc['email']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có bác sĩ nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Y tá -->
    <div class="col-lg-6">
        <div class="content-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-user-nurse me-2"></i>Y tá (<?= count($nurses) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($nurses)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Họ tên</th><th>Email</th></tr></thead>
                        <tbody>
                            <?php foreach ($nurses as $n): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($n['name']) ?></strong></td>
                                <td><?= htmlspecialchars($n['email']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có y tá nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
