<?php
/**
 * Medicine Model - Quản lý thuốc + tồn kho
 */
require_once __DIR__ . '/../config/database.php';

class Medicine {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả thuốc
    public function getAll() {
        $sql = "SELECT * FROM medicines ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm thuốc theo ID
    public function findById($id) {
        $sql = "SELECT * FROM medicines WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo thuốc mới
    public function create($data) {
        $sql = "INSERT INTO medicines (name, description, quantity, expiry_date, price) 
                VALUES (:name, :description, :quantity, :expiry_date, :price)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':expiry_date', $data['expiry_date']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Cập nhật thuốc
    public function update($id, $data) {
        $sql = "UPDATE medicines SET name = :name, description = :description, 
                quantity = :quantity, expiry_date = :expiry_date, price = :price 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':expiry_date', $data['expiry_date']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Xóa thuốc
    public function delete($id) {
        $sql = "DELETE FROM medicines WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Đếm tổng thuốc
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM medicines";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Lấy thuốc sắp hết hạn (trong 30 ngày)
    public function getExpiringSoon() {
        $sql = "SELECT * FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                AND expiry_date >= CURDATE() ORDER BY expiry_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy thuốc hết hàng (quantity <= 10)
    public function getLowStock() {
        $sql = "SELECT * FROM medicines WHERE quantity <= 10 ORDER BY quantity ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Trừ tồn kho thuốc khi kê đơn
     * @param int $medicineId
     * @param int $quantity - Số lượng cần trừ (mặc định 1 đơn vị)
     * @return bool
     * @throws Exception nếu không đủ tồn kho
     */
    public function deductStock($medicineId, $quantity = 1) {
        $medicine = $this->findById($medicineId);
        if (!$medicine) {
            throw new Exception("Không tìm thấy thuốc ID: $medicineId");
        }
        if ($medicine['quantity'] < $quantity) {
            throw new Exception("Thuốc '{$medicine['name']}' không đủ tồn kho (còn {$medicine['quantity']}, cần $quantity).");
        }
        $sql = "UPDATE medicines SET quantity = quantity - :qty WHERE id = :id AND quantity >= :qty";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $medicineId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new Exception("Không thể trừ tồn kho thuốc '{$medicine['name']}'.");
        }
        return true;
    }

    /**
     * Đặt trước tồn kho thuốc khi Bác sĩ kê đơn (chưa trừ kho vật lý)
     */
    public function reserveStock($medicineId, $quantity = 1) {
        $medicine = $this->findById($medicineId);
        if (!$medicine) {
            throw new Exception("Không tìm thấy thuốc ID: $medicineId");
        }
        $available = $medicine['quantity'] - ($medicine['reserved'] ?? 0);
        if ($available < $quantity) {
            throw new Exception("Thuốc '{$medicine['name']}' không đủ tồn kho khả dụng (tổng tồn {$medicine['quantity']}, đã đặt trước {$medicine['reserved']}, cần thêm $quantity).");
        }
        $sql = "UPDATE medicines SET reserved = COALESCE(reserved, 0) + :qty WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $medicineId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    /**
     * Giải phóng số lượng đặt trước khi Đơn thuốc bị hủy
     */
    public function releaseStock($medicineId, $quantity = 1) {
        $sql = "UPDATE medicines SET reserved = GREATEST(0, COALESCE(reserved, 0) - :qty) WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $medicineId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    /**
     * Trừ tồn kho vật lý thực tế khi Dược sĩ giao thuốc (đồng thời trừ số lượng đặt trước)
     */
    public function dispenseStock($medicineId, $quantity = 1) {
        $medicine = $this->findById($medicineId);
        if (!$medicine) {
            throw new Exception("Không tìm thấy thuốc ID: $medicineId");
        }
        $sql = "UPDATE medicines SET 
                    quantity = GREATEST(0, quantity - :qty),
                    reserved = GREATEST(0, COALESCE(reserved, 0) - :qty_res)
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':qty_res', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $medicineId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    /**
     * Kiểm tra thuốc đã hết hạn chưa
     * @param int $medicineId
     * @return bool - true nếu đã hết hạn
     */
    public function isExpired($medicineId) {
        $sql = "SELECT expiry_date FROM medicines WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $medicineId);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row || empty($row['expiry_date'])) return false;
        return strtotime($row['expiry_date']) < strtotime(date('Y-m-d'));
    }
}
