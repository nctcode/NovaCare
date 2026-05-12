<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-history me-2"></i>Nhật Ký Hệ Thống (Audit Logs)
                        </h6>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Người thao tác</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hành động</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bảng</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID Record</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($logs)): ?>
                                    <tr><td colspan="6" class="text-center py-4">Chưa có nhật ký nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm font-weight-bold"><?= htmlspecialchars($log['user_name'] ?? 'Hệ thống') ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <?php 
                                                    $badgeClass = 'bg-gradient-secondary';
                                                    if ($log['action'] == 'INSERT') $badgeClass = 'bg-gradient-success';
                                                    if ($log['action'] == 'UPDATE') $badgeClass = 'bg-gradient-warning';
                                                    if ($log['action'] == 'DELETE') $badgeClass = 'bg-gradient-danger';
                                                    if ($log['action'] == 'LOGIN') $badgeClass = 'bg-gradient-info';
                                                ?>
                                                <span class="badge badge-sm <?= $badgeClass ?>"><?= $log['action'] ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-xs font-weight-bold"><?= $log['table_name'] ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-xs font-weight-bold">#<?= $log['record_id'] ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" class="btn btn-link text-secondary mb-0" data-bs-toggle="modal" data-bs-target="#logModal<?= $log['id'] ?>">
                                                    <i class="fas fa-eye text-primary"></i> Xem JSON
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="logModal<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Chi tiết thay đổi (#<?= $log['id'] ?>)</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-light border border-info text-sm py-2 px-3 mb-3">
                                                                    <strong><i class="fas fa-info-circle text-info me-1"></i> Bối cảnh thao tác:</strong> 
                                                                    <?php 
                                                                        if($log['action'] == 'LOGIN') echo 'Người dùng đã <b>đăng nhập</b> vào hệ thống bằng Email bên dưới.';
                                                                        elseif($log['action'] == 'LOGOUT') echo 'Người dùng đã <b>đăng xuất</b> khỏi hệ thống.';
                                                                        elseif($log['action'] == 'INSERT') echo 'Người dùng đã <b>tạo mới</b> một bản ghi trong bảng <code>' . $log['table_name'] . '</code>.';
                                                                        elseif($log['action'] == 'UPDATE') echo 'Người dùng đã <b>chỉnh sửa</b> bản ghi ID #' . $log['record_id'] . ' trong bảng <code>' . $log['table_name'] . '</code>.';
                                                                        elseif($log['action'] == 'DELETE') echo 'Người dùng đã <b>xóa (xóa mềm)</b> bản ghi ID #' . $log['record_id'] . ' trong bảng <code>' . $log['table_name'] . '</code>.';
                                                                    ?>
                                                                </div>
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-sm text-sm">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th>Trường dữ liệu</th>
                                                                                <th class="text-danger">Dữ liệu cũ</th>
                                                                                <th class="text-success">Dữ liệu mới</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                                $oldData = $log['old_values'] ? json_decode($log['old_values'], true) : [];
                                                                                $newData = $log['new_values'] ? json_decode($log['new_values'], true) : [];
                                                                                
                                                                                // Lấy tất cả các keys từ cả 2 mảng
                                                                                $allKeys = array_unique(array_merge(array_keys($oldData ?: []), array_keys($newData ?: [])));
                                                                                
                                                                                if(empty($allKeys)):
                                                                            ?>
                                                                                <tr><td colspan="3" class="text-center text-muted">Không có dữ liệu chi tiết</td></tr>
                                                                            <?php else: ?>
                                                                                <?php foreach($allKeys as $key): ?>
                                                                                    <tr>
                                                                                        <td class="font-weight-bold"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?></td>
                                                                                        <td class="text-danger text-wrap" style="max-width: 250px;">
                                                                                            <?= isset($oldData[$key]) ? htmlspecialchars(is_array($oldData[$key]) ? json_encode($oldData[$key]) : $oldData[$key]) : '<i class="text-muted">N/A</i>' ?>
                                                                                        </td>
                                                                                        <td class="text-success text-wrap" style="max-width: 250px;">
                                                                                            <?= isset($newData[$key]) ? htmlspecialchars(is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key]) : '<i class="text-muted">N/A</i>' ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-primary">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="index.php?page=audit_logs&p=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
