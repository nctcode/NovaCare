<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Filter Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white pb-3 border-bottom">
                    <h6 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-filter me-2"></i>Bộ Lọc Tìm Kiếm
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3 align-items-end">
                        <input type="hidden" name="page" value="audit_logs">
                        
                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Loại log</label>
                            <select name="log_type" class="form-select form-select-sm shadow-none border-radius-md">
                                <option value="">-- Tất cả --</option>
                                <?php foreach ($availableLogTypes as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= (isset($_GET['log_type']) && $_GET['log_type'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Hành động</label>
                            <select name="action" class="form-select form-select-sm shadow-none border-radius-md">
                                <option value="">-- Tất cả --</option>
                                <?php foreach ($availableActions as $act): ?>
                                    <option value="<?= $act ?>" <?= (isset($_GET['action']) && $_GET['action'] == $act) ? 'selected' : '' ?>><?= $act ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Module (Bảng)</label>
                            <select name="table_name" class="form-select form-select-sm shadow-none border-radius-md">
                                <option value="">-- Tất cả --</option>
                                <?php foreach ($availableTables as $tbl): ?>
                                    <option value="<?= htmlspecialchars($tbl) ?>" <?= (isset($_GET['table_name']) && $_GET['table_name'] == $tbl) ? 'selected' : '' ?>><?= htmlspecialchars($tbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Người thao tác</label>
                            <input type="text" name="user_name" class="form-control form-control-sm shadow-none border-radius-md" placeholder="Tên người dùng..." value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Từ ngày</label>
                            <input type="date" name="date_from" class="form-control form-control-sm shadow-none border-radius-md" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label text-xs font-weight-bolder text-secondary">Đến ngày</label>
                            <input type="date" name="date_to" class="form-control form-control-sm shadow-none border-radius-md" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                        </div>

                        <div class="col-12 mt-3 text-end">
                            <a href="index.php?page=audit_logs" class="btn btn-sm btn-light mb-0 me-2 border"><i class="fas fa-undo me-1"></i>Xóa lọc</a>
                            <button type="submit" class="btn btn-sm btn-primary mb-0 shadow-sm"><i class="fas fa-search me-1"></i>Tìm kiếm</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-history me-2"></i>Nhật Ký Hệ Thống (Audit Logs)
                        </h6>
                        <span class="badge bg-gradient-info rounded-pill">Tổng: <?= number_format($totalLogs) ?> bản ghi</span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2 mt-3">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User (Người thao tác)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hành động</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Module</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($logs)): ?>
                                    <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>Không tìm thấy nhật ký nào phù hợp.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td class="align-middle px-4">
                                                <span class="text-secondary text-xs font-weight-bold">#<?= $log['id'] ?></span>
                                            </td>
                                            <td class="px-4">
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <img src="assets/img/team-2.jpg" class="avatar avatar-sm me-3 bg-light" alt="user" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($log['user_name'] ?? 'SYS') ?>&background=random'">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= htmlspecialchars($log['user_name'] ?? 'Hệ thống') ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle px-4">
                                                <?php 
                                                    $badgeClass = 'bg-gradient-secondary';
                                                    if ($log['action'] == 'INSERT') $badgeClass = 'bg-gradient-success';
                                                    if ($log['action'] == 'UPDATE') $badgeClass = 'bg-gradient-warning';
                                                    if ($log['action'] == 'DELETE') $badgeClass = 'bg-gradient-danger';
                                                    if ($log['action'] == 'LOGIN') $badgeClass = 'bg-gradient-info';
                                                ?>
                                                <span class="badge badge-sm <?= $badgeClass ?> px-3 py-1 shadow-sm rounded-pill mb-1 d-inline-block"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> <?= $log['action'] ?></span>
                                                <br>
                                                <?php if(!empty($log['log_type'])): ?>
                                                    <small class="text-xs text-muted"><?= htmlspecialchars(ucfirst($log['log_type'])) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle px-4">
                                                <span class="text-secondary text-xs font-weight-bold border px-2 py-1 rounded bg-light"><?= $log['table_name'] ?></span>
                                                <?php if($log['record_id']): ?>
                                                    <div class="text-xs text-muted mt-1">ID: #<?= $log['record_id'] ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle px-4">
                                                <span class="text-secondary text-xs"><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></span>
                                            </td>
                                            <td class="align-middle px-4">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        <i class="far fa-calendar-alt me-1"></i><?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                                    </span>
                                                    <span class="text-secondary text-xs">
                                                        <i class="far fa-clock me-1"></i><?= date('H:i:s', strtotime($log['created_at'])) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm mb-0 rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#logModal<?= $log['id'] ?>">
                                                    <i class="fas fa-eye"></i> Xem JSON
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="logModal<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title text-primary"><i class="fas fa-search-plus me-2"></i>Chi tiết thay đổi (#<?= $log['id'] ?>)</h5>
                                                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <div class="alert alert-light border-start border-4 border-info text-sm py-3 px-3 mb-4 shadow-sm">
                                                                    <strong class="text-dark"><i class="fas fa-info-circle text-info me-1"></i> Bối cảnh thao tác:</strong><br>
                                                                    <span class="text-secondary mt-1 d-block">
                                                                    <?php 
                                                                        if($log['action'] == 'LOGIN') echo 'Người dùng đã <b>đăng nhập</b> vào hệ thống.';
                                                                        elseif($log['action'] == 'LOGOUT') echo 'Người dùng đã <b>đăng xuất</b> khỏi hệ thống.';
                                                                        elseif($log['action'] == 'INSERT') echo 'Người dùng đã <b>tạo mới</b> một bản ghi trong bảng <code class="bg-light px-2 py-1 rounded text-dark border">' . $log['table_name'] . '</code>.';
                                                                        elseif($log['action'] == 'UPDATE') echo 'Người dùng đã <b>chỉnh sửa</b> bản ghi ID #' . $log['record_id'] . ' trong bảng <code class="bg-light px-2 py-1 rounded text-dark border">' . $log['table_name'] . '</code>.';
                                                                        elseif($log['action'] == 'DELETE') echo 'Người dùng đã <b>xóa</b> bản ghi ID #' . $log['record_id'] . ' trong bảng <code class="bg-light px-2 py-1 rounded text-dark border">' . $log['table_name'] . '</code>.';
                                                                    ?>
                                                                    </span>
                                                                </div>
                                                                <div class="table-responsive border rounded">
                                                                    <table class="table table-bordered table-sm text-sm mb-0">
                                                                        <thead class="bg-light text-center">
                                                                            <tr>
                                                                                <th width="30%">Trường dữ liệu</th>
                                                                                <th width="35%" class="text-danger"><i class="fas fa-minus-circle me-1"></i>Dữ liệu cũ</th>
                                                                                <th width="35%" class="text-success"><i class="fas fa-plus-circle me-1"></i>Dữ liệu mới</th>
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
                                                                                <tr><td colspan="3" class="text-center text-muted py-3">Không có dữ liệu chi tiết</td></tr>
                                                                            <?php else: ?>
                                                                                <?php foreach($allKeys as $key): ?>
                                                                                    <tr>
                                                                                        <td class="font-weight-bold align-middle bg-light text-dark px-3"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?></td>
                                                                                        <td class="text-danger text-wrap align-middle px-3" style="max-width: 250px; background-color: #fff5f5;">
                                                                                            <?= isset($oldData[$key]) ? htmlspecialchars(is_array($oldData[$key]) ? json_encode($oldData[$key]) : $oldData[$key]) : '<i class="text-muted">N/A</i>' ?>
                                                                                        </td>
                                                                                        <td class="text-success text-wrap align-middle px-3" style="max-width: 250px; background-color: #f0fff4;">
                                                                                            <?= isset($newData[$key]) ? htmlspecialchars(is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key]) : '<i class="text-muted">N/A</i>' ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
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
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                        <div class="text-sm text-secondary">
                            Hiển thị từ <?= $offset + 1 ?> đến <?= min($offset + $limit, $totalLogs) ?> trong số <?= $totalLogs ?> bản ghi
                        </div>
                        <?php
                            // Giữ lại các param filter khi chuyển trang
                            $queryParams = $_GET;
                            unset($queryParams['p']); // Xóa param p hiện tại
                            $queryString = http_build_query($queryParams);
                            $baseUrl = "index.php?" . $queryString . "&p=";
                        ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-primary mb-0">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl . ($page - 1) ?>"><i class="fas fa-angle-left"></i></a>
                                </li>
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $baseUrl . $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl . ($page + 1) ?>"><i class="fas fa-angle-right"></i></a>
                                </li>
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
