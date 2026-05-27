

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa-solid fa-users-gear me-2"></i>Quản lý Tài khoản</h5>
        <a href="index.php?page=users&action=create" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i>Thêm tài khoản
        </a>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu tài khoản.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>#<?= $u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2 bg-primary text-white" style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($u['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['phone']) ?></td>
                                <td>
                                    <?php
                                    $roleClass = 'bg-secondary';
                                    $roleName = $u['role'];
                                    switch ($u['role']) {
                                        case 'admin': $roleClass = 'bg-danger'; $roleName = 'Quản trị viên'; break;
                                        case 'doctor': $roleClass = 'bg-primary'; $roleName = 'Bác sĩ'; break;
                                        case 'nurse': $roleClass = 'bg-info text-dark'; $roleName = 'Điều dưỡng'; break;
                                        case 'patient': $roleClass = 'bg-success'; $roleName = 'Bệnh nhân'; break;
                                        case 'receptionist': $roleClass = 'bg-warning text-dark'; $roleName = 'Lễ tân'; break;
                                        case 'pharmacist': $roleClass = 'bg-primary'; $roleName = 'Dược sĩ'; break;
                                        case 'technician': $roleClass = 'bg-secondary'; $roleName = 'Kỹ thuật viên'; break;
                                        case 'director': $roleClass = 'bg-dark'; $roleName = 'Ban giám đốc'; break;
                                        case 'cashier': $roleClass = 'bg-success'; $roleName = 'Thu ngân'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $roleClass ?>"><?= htmlspecialchars($roleName) ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                                <td class="text-end">
                                    <a href="index.php?page=users&action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa" onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')">
                                            <i class="fa-solid fa-trash"></i>
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

<!-- Modal Xóa User -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="index.php?page=users&action=delete">
            <input type="hidden" name="csrf_token" value="<?= Security::getCsrfToken() ?>">
            <input type="hidden" name="id" id="deleteId">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa tài khoản <strong id="deleteName"></strong>?</p>
                <p class="text-danger small mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i>Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger">Xóa tài khoản</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteName').innerText = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

