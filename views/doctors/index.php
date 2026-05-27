<!-- Doctor List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="card-header">
        <h5><i class="fa-solid fa-user-doctor me-2"></i>Danh sách Bác sĩ</h5>
        <div class="table-search-bar">
            <div class="table-search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="table-search-js" data-table="doctorTable" placeholder="Tìm kiếm...">
            </div>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="index.php?page=doctors&action=create" class="btn-action btn-add" id="btnAddDoctor">
                <i class="fa-solid fa-plus"></i> Thêm bác sĩ
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="doctorTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Khoa</th>
                        <th>Chuyên khoa</th>
                        <th>Kinh nghiệm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doctors)): ?>
                        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-user-doctor"></i></div><h6>Chưa có bác sĩ</h6><p>Nhấn "Thêm bác sĩ" để bắt đầu.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($doctors as $idx => $d): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($d['name']) ?></strong></td>
                            <td><?= htmlspecialchars($d['email']) ?></td>
                            <td><?= htmlspecialchars($d['phone'] ?? '') ?></td>
                            <td>
                                <?php
                                $depts = !empty($d['department_name']) ? explode(', ', $d['department_name']) : [];
                                if (empty($depts)):
                                ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php else: ?>
                                    <?php foreach ($depts as $deptName): ?>
                                        <span class="badge bg-primary-light text-primary me-1 mb-1" style="font-size: 11px; font-weight: 500; border-radius: 6px;"><?= htmlspecialchars($deptName) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($d['specialty'] ?? '') ?></td>
                            <td><?= $d['experience_years'] ?? 0 ?> năm</td>
                            <td>
                                <a href="index.php?page=doctors&action=view&id=<?= $d['id'] ?>" 
                                   class="btn-action btn-view text-primary me-2" title="Xem chi tiết" style="background-color: var(--primary-light); color: var(--primary); padding: 5px 10px; border-radius: 8px; font-size: 13px; text-decoration: none;">
                                    <i class="bi bi-eye-fill"></i> Xem
                                </a>
                                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                <a href="index.php?page=doctors&action=edit&id=<?= $d['id'] ?>" 
                                   class="btn-action btn-edit text-warning me-2" title="Sửa" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 5px 10px; border-radius: 8px; font-size: 13px; text-decoration: none;">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <button onclick="confirmDelete('index.php?page=doctors&action=delete&id=<?= $d['id'] ?>')" 
                                        class="btn-action btn-delete text-danger" title="Xóa" style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 5px 10px; border-radius: 8px; font-size: 13px; border: none;">
                                    <i class="bi bi-trash3"></i> Xóa
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
