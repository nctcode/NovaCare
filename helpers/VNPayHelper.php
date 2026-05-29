<?php
/**
 * VNPayHelper - Hỗ trợ tích hợp thanh toán VNPay Sandbox
 */
class VNPayHelper {
    private static $tmnCode = '2QXG2A8G'; // Sandbox Terminal ID
    private static $hashSecret = '9D8WNDL2N0WD81KNDH2UDHNWKDJW91JS'; // Sandbox Hash Secret
    private static $apiUrl = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

    /**
     * Tạo URL thanh toán VNPay
     */
    public static function createPaymentUrl($invoiceId, $amount, $returnUrl, $ipAddress) {
        $txnRef = $invoiceId . '_' . time();
        
        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => self::$tmnCode,
            'vnp_Amount' => intval($amount * 100), // Nhân 100 theo yêu cầu VNPay
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $ipAddress,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan hoa don NovaCare #' . $invoiceId,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $txnRef
        ];

        ksort($params);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = self::$apiUrl . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, self::$hashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return [
            'url' => $vnp_Url,
            'txn_ref' => $txnRef
        ];
    }

    /**
     * Xác minh chữ ký phản hồi từ VNPay
     */
    public static function verifyResponse($getParams) {
        $vnp_SecureHash = $getParams['vnp_SecureHash'] ?? '';
        
        // Loại bỏ các trường hash để kiểm tra chữ ký
        $params = [];
        foreach ($getParams as $key => $val) {
            if (substr($key, 0, 4) == 'vnp_' && $key != 'vnp_SecureHash' && $key != 'vnp_SecureHashType') {
                $params[$key] = $val;
            }
        }
        
        ksort($params);
        $i = 0;
        $hashdata = "";
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, self::$hashSecret);
        return hash_equals($secureHash, $vnp_SecureHash);
    }
}
