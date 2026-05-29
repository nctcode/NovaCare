<!-- Patient List -->
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
        <h5><i class="fa-solid fa-hospital-user me-2"></i>Danh sách Bệnh nhân</h5>
        <div class="table-search-bar">
            <div class="table-search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="table-search-js" data-table="patientTable" placeholder="Tìm kiếm...">
            </div>
            <?php if (in_array($_SESSION['user']['role'], ['admin', 'receptionist'])): ?>
            <a href="index.php?page=patients&action=create" class="btn-action btn-add" id="btnAddPatient">
                <i class="fa-solid fa-plus"></i> Thêm bệnh nhân
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="patientTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã BN</th>
                        <th>Họ tên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>SĐT</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-hospital-user"></i></div><h6>Chưa có bệnh nhân</h6><p>Nhấn "Thêm bệnh nhân" để bắt đầu.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($patients as $idx => $p): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 12px; font-weight: 600; border-radius: 5px;">BN<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></span></td>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><?= $p['date_of_birth'] ? date('d/m/Y', strtotime($p['date_of_birth'])) : '' ?></td>
                            <td>
                                <span class="badge-status badge-<?= $p['gender'] ?? '' ?>">
                                    <?php
                                        $genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                        echo $genderMap[$p['gender']] ?? '';
                                    ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['phone'] ?? '-') ?></td>
                            <td class="td-actions text-end">
                                <div class="action-btn-group justify-content-end">
                                    <a href="index.php?page=patients&action=view&id=<?= $p['id'] ?>" 
                                       class="btn-action btn-view" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                    <?php if (in_array($_SESSION['user']['role'], ['admin', 'receptionist'])): ?>
                                    <a href="index.php?page=patients&action=edit&id=<?= $p['id'] ?>" 
                                       class="btn-action btn-edit" title="Sửa">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <button onclick="confirmDelete('index.php?page=patients&action=delete&id=<?= $p['id'] ?>')" 
                                             class="btn-action btn-delete" title="Xóa">
                                        <i class="bi bi-trash3"></i> Xóa
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
