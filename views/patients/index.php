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
        <h5><i class="bi bi-people-fill me-2"></i>Danh sách Bệnh nhân</h5>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?page=patients&action=create" class="btn-action btn-add" id="btnAddPatient">
            <i class="bi bi-plus-lg"></i> Thêm bệnh nhân
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="patientTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Nhóm máu</th>
                        <th>Địa chỉ</th>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
                    <?php else: ?>
                        <?php foreach ($patients as $idx => $p): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><?= htmlspecialchars($p['email']) ?></td>
                            <td><?= htmlspecialchars($p['phone'] ?? '') ?></td>
                            <td><?= $p['date_of_birth'] ? date('d/m/Y', strtotime($p['date_of_birth'])) : '' ?></td>
                            <td>
                                <span class="badge-status badge-<?= $p['gender'] ?? '' ?>">
                                    <?php
                                        $genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                        echo $genderMap[$p['gender']] ?? '';
                                    ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($p['blood_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['address'] ?? '') ?></td>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <td>
                                <a href="index.php?page=patients&action=edit&id=<?= $p['id'] ?>" 
                                   class="btn-action btn-edit" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="confirmDelete('index.php?page=patients&action=delete&id=<?= $p['id'] ?>')" 
                                        class="btn-action btn-delete" title="Xóa">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
