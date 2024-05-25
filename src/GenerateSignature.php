<?php

namespace Wslib;

class GenerateSignature
{
    protected $base64Signature;

    public function __construct($totalAmount, $transactionUUID, $secretKey, $productCode = "EPAYTEST")
    {

        $data = "total_amount=" . $totalAmount . ",transaction_uuid=" . $transactionUUID . ",product_code=" . $productCode;
        $signature = hash_hmac('sha256', $data, $secretKey, true);
        $this->base64Signature = base64_encode($signature);
    }

    public function get()
    {
        return $this->base64Signature;
    }
}
