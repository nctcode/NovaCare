<?php
/**
 * RequestValidator - Hỗ trợ validate đầu vào dữ liệu cho API
 */
class RequestValidator {

    private $errors = [];

    /**
     * Lấy danh sách các lỗi hiện tại
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Kiểm tra xem có lỗi nào không
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Bắt buộc các trường không được trống
     */
    public function required($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $this->errors[$field] = "Trường này là bắt buộc.";
            }
        }
        return $this;
    }

    /**
     * Kiểm tra định dạng Email
     */
    public function email($email, $field = 'email') {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Định dạng email không hợp lệ.";
        }
        return $this;
    }

    /**
     * Kiểm tra độ dài tối thiểu của chuỗi
     */
    public function minLength($value, $min, $field) {
        if (!empty($value) && mb_strlen($value, 'UTF-8') < $min) {
            $this->errors[$field] = "Độ dài tối thiểu phải là {$min} ký tự.";
        }
        return $this;
    }

    /**
     * So khớp hai giá trị (VD: password & confirm_password)
     */
    public function matches($value1, $value2, $field2, $message = "Mật khẩu xác nhận không khớp.") {
        if ($value1 !== $value2) {
            $this->errors[$field2] = $message;
        }
        return $this;
    }

    /**
     * Kiểm tra định dạng số điện thoại Việt Nam (10 chữ số)
     */
    public function phone($phone, $field = 'phone') {
        if (!empty($phone)) {
            $cleaned = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($cleaned) < 10 || strlen($cleaned) > 11 || !preg_match('/^(03|05|07|08|09|01[2689])[0-9]{8}$/', $cleaned)) {
                $this->errors[$field] = "Số điện thoại không hợp lệ (phải gồm 10 chữ số bắt đầu bằng đầu số Việt Nam).";
            }
        }
        return $this;
    }

    /**
     * Kiểm tra định dạng ngày sinh YYYY-MM-DD
     */
    public function date($dateStr, $field = 'date_of_birth') {
        if (!empty($dateStr)) {
            $d = DateTime::createFromFormat('Y-m-d', $dateStr);
            if (!$d || $d->format('Y-m-d') !== $dateStr) {
                $this->errors[$field] = "Ngày sinh phải đúng định dạng YYYY-MM-DD.";
            }
        }
        return $this;
    }

    /**
     * Kiểm tra giá trị thuộc tập hợp cho trước
     */
    public function inArray($value, $array, $field, $message = "Giá trị không hợp lệ.") {
        if (!empty($value) && !in_array($value, $array)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }
}
