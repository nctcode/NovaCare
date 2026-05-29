<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- Content Card and Wrapper -->
<div class="row g-4 mb-4" data-aos="fade-down">
    <div class="col-12">
        <!-- Advanced Filter Card -->
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
            <div class="d-flex align-items-center gap-2 mb-3.5">
                <div class="d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-filter"></i>
                </div>
                <h6 class="fw-bold m-0 text-dark">Bộ lọc tra cứu Nhật ký Hệ thống</h6>
            </div>
            
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="page" value="audit_logs">
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Phân loại</label>
                    <select name="log_type" class="form-select py-2" style="border-radius: 10px; font-size: 13px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($availableLogTypes as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (isset($_GET['log_type']) && $_GET['log_type'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Hành động</label>
                    <select name="action" class="form-select py-2" style="border-radius: 10px; font-size: 13px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($availableActions as $act): ?>
                            <option value="<?= $act ?>" <?= (isset($_GET['action']) && $_GET['action'] == $act) ? 'selected' : '' ?>><?= $act ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Phân hệ (Bảng)</label>
                    <select name="table_name" class="form-select py-2" style="border-radius: 10px; font-size: 13px;">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($availableTables as $tbl): ?>
                            <option value="<?= htmlspecialchars($tbl) ?>" <?= (isset($_GET['table_name']) && $_GET['table_name'] == $tbl) ? 'selected' : '' ?>><?= htmlspecialchars($tbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Người thực hiện</label>
                    <input type="text" name="user_name" class="form-control py-2" placeholder="Tên người dùng..." value="<?= htmlspecialchars($_GET['user_name'] ?? '') ?>" style="border-radius: 10px; font-size: 13px;">
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control py-2" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" style="border-radius: 10px; font-size: 13px;">
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold text-secondary" style="font-size: 12.5px;">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control py-2" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" style="border-radius: 10px; font-size: 13px;">
                </div>

                <div class="col-12 mt-3 text-end">
                    <a href="index.php?page=audit_logs" class="btn btn-outline-secondary px-3 py-2 me-2" style="border-radius: 10px; font-size: 13px; font-weight: 600;">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-size: 13px; font-weight: 600;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tra cứu nhật ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Log Data Card -->
<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Nhật ký Thay đổi hệ thống (Audit Logs)</h5>
        <span class="badge bg-light text-secondary border px-3 py-1.5" style="border-radius: 12px; font-size: 12px; font-weight: 600;">
            Tổng cộng: <?= number_format($totalLogs) ?> dòng ghi
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Mã log</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                        <th>Bảng & Khóa</th>
                        <th>Địa chỉ IP</th>
                        <th>Thời gian</th>
                        <th style="width: 140px; text-align: center;">Chi tiết thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-2" style="font-size: 24px;"></i>
                            Không tìm thấy nhật ký phù hợp với bộ lọc tìm kiếm.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                #<?= $log['id'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                     style="width: 32px; height: 32px; border-radius: 50%; font-size: 12px; background: linear-gradient(135deg, #64748b, #475569);">
                                    <?= strtoupper(substr($log['user_name'] ?? 'SYS', 0, 1)) ?>
                                </div>
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 14px;"><?= htmlspecialchars($log['user_name'] ?? 'Hệ thống') ?></strong>
                                    <span class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($log['log_type'] ? ucfirst($log['log_type']) : 'Data') ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $badgeStyle = 'background-color: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.2);';
                            if ($log['action'] == 'INSERT') {
                                $badgeStyle = 'background-color: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);';
                            } elseif ($log['action'] == 'UPDATE') {
                                $badgeStyle = 'background-color: rgba(249, 115, 22, 0.1); color: #f97316; border: 1px solid rgba(249, 115, 22, 0.2);';
                            } elseif ($log['action'] == 'DELETE') {
                                $badgeStyle = 'background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);';
                            } elseif ($log['action'] == 'LOGIN') {
                                $badgeStyle = 'background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);';
                            } elseif ($log['action'] == 'LOGOUT') {
                                $badgeStyle = 'background-color: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.2);';
                            }
                            ?>
                            <span class="badge text-uppercase" style="font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 6px; <?= $badgeStyle ?>">
                                <?= $log['action'] ?>
                            </span>
                        </td>
                        <td>
                            <code class="bg-light text-dark px-2 py-0.5 rounded border-0" style="font-size: 12.5px; font-weight: 500;">
                                <?= htmlspecialchars($log['table_name']) ?>
                            </code>
                            <?php if($log['record_id']): ?>
                                <span class="text-muted ms-1" style="font-size: 11px;">ID: #<?= $log['record_id'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-secondary" style="font-size: 12.5px; font-family: monospace;">
                                <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark" style="font-size: 13px; font-weight: 500;">
                                    <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                </span>
                                <span class="text-muted" style="font-size: 11px;">
                                    <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="font-size: 11.5px; font-weight: 600; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#logModal<?= $log['id'] ?>">
                                    <i class="fa-solid fa-code me-1"></i> So sánh dữ liệu
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top gap-3" style="background: var(--gray-50);">
            <div class="text-sm text-secondary" style="font-size: 13px;">
                Hiển thị từ <strong><?= $offset + 1 ?></strong> đến <strong><?= min($offset + $limit, $totalLogs) ?></strong> trong số <strong><?= $totalLogs ?></strong> dòng ghi
            </div>
            <?php
                $queryParams = $_GET;
                unset($queryParams['p']);
                $queryString = http_build_query($queryParams);
                $baseUrl = "index.php?" . $queryString . "&p=";
            ?>
            <nav>
                <ul class="pagination pagination-sm m-0 gap-1">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link border" href="<?= $baseUrl . ($page - 1) ?>" style="border-radius: 6px; padding: 6px 10px;"><i class="fa-solid fa-angle-left"></i></a>
                    </li>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link border" href="<?= $baseUrl . $i ?>" style="border-radius: 6px; padding: 6px 12px;"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link border" href="<?= $baseUrl . ($page + 1) ?>" style="border-radius: 6px; padding: 6px 10px;"><i class="fa-solid fa-angle-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal container loop at root level to prevent z-index backdrop issue -->
<?php if(!empty($logs)): ?>
    <?php foreach ($logs as $log): ?>
        <div class="modal fade" id="logModal<?= $log['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <div class="modal-header bg-light border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark" style="font-size: 16px;">
                            <i class="fa-solid fa-code-compare text-primary me-2"></i>Chi tiết Thay đổi Nhật ký #<?= $log['id'] ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Context Banner -->
                        <div class="alert alert-light border-0 py-3 px-3 mb-4 d-flex align-items-start gap-3" style="border-radius: 10px; background-color: var(--gray-50);">
                            <div class="text-primary mt-0.5" style="font-size: 16px;">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div>
                                <strong class="text-dark d-block mb-1" style="font-size: 13.5px;">Bối cảnh hành động:</strong>
                                <span class="text-secondary" style="font-size: 12.5px; line-height: 1.5;">
                                <?php 
                                    if($log['action'] == 'LOGIN') echo 'Người dùng thực hiện đăng nhập vào cổng thông tin.';
                                    elseif($log['action'] == 'LOGOUT') echo 'Người dùng thực hiện đăng xuất khỏi hệ thống.';
                                    elseif($log['action'] == 'INSERT') echo 'Người dùng đã tạo mới một dòng dữ liệu trong bảng <code>' . $log['table_name'] . '</code>.';
                                    elseif($log['action'] == 'UPDATE') echo 'Người dùng đã cập nhật thông tin dòng ghi ID #' . $log['record_id'] . ' thuộc bảng <code>' . $log['table_name'] . '</code>.';
                                    elseif($log['action'] == 'DELETE') echo 'Người dùng đã xóa bỏ dòng ghi ID #' . $log['record_id'] . ' khỏi bảng <code>' . $log['table_name'] . '</code>.';
                                ?>
                                </span>
                            </div>
                        </div>

                        <!-- Diff Table -->
                        <div class="table-responsive border" style="border-radius: 10px;">
                            <table class="table table-bordered mb-0 align-middle" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="30%" class="fw-bold text-dark px-3 py-2.5">Trường</th>
                                        <th width="35%" class="fw-bold text-danger px-3 py-2.5"><i class="fa-solid fa-circle-minus me-1"></i>Giá trị cũ</th>
                                        <th width="35%" class="fw-bold text-success px-3 py-2.5"><i class="fa-solid fa-circle-plus me-1"></i>Giá trị mới</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $oldData = $log['old_values'] ? json_decode($log['old_values'], true) : [];
                                        $newData = $log['new_values'] ? json_decode($log['new_values'], true) : [];
                                        $allKeys = array_unique(array_merge(array_keys($oldData ?: []), array_keys($newData ?: [])));
                                        
                                        if(empty($allKeys)):
                                    ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Không có thông tin chi tiết (Dữ liệu rỗng).</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($allKeys as $key): ?>
                                            <tr>
                                                <td class="fw-semibold bg-light text-dark px-3 py-2" style="font-size: 12.5px;">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $key)) ?>
                                                </td>
                                                <td class="text-danger px-3 py-2" style="background-color: #fef2f2; font-family: monospace; word-break: break-all;">
                                                    <?= isset($oldData[$key]) ? htmlspecialchars(is_array($oldData[$key]) ? json_encode($oldData[$key]) : $oldData[$key]) : '<span class="text-muted font-normal italic">N/A</span>' ?>
                                                </td>
                                                <td class="text-success px-3 py-2" style="background-color: #f0fdf4; font-family: monospace; word-break: break-all;">
                                                    <?= isset($newData[$key]) ? htmlspecialchars(is_array($newData[$key]) ? json_encode($newData[$key]) : $newData[$key]) : '<span class="text-muted font-normal italic">N/A</span>' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-secondary px-3.5 py-1.5" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; font-size: 13px;">Đóng lại</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
