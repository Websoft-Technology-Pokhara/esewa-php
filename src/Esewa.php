<?php

namespace Wslib;

use Wslib\GenerateSignature;


class Esewa
{
    // URL for production
    private static string $proURL = 'https://epay.esewa.com.np/api/epay/main/v2/form';
    // URL for development
    private static string $devURL = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
    // Environment [Dev, Prod]
    private static string $env;
    // Secret Key
    private static string $secretKey = "8gBm/:&EnhH.1/q";
    // Transaction UUID
    private static string $transactionUUID;


    /*
    * @param string $env
    * @return self
    */
    public static function init(string $env = "DEV"): self
    {
        $array = ["DEV", "PROD"];
        self::$env = in_array($env, $array) ? $env : "DEV";

        return new self;
    }

    /*
    * @param string $successURL
    * @param string $failureURL
    * @param float $amount
    * @param string $productCode
    * @param float $taxAmount
    * @param float $productDeliveryCharge
    * @param float $productServiceCharge
    * @return void
    */
    public static function config($t, string $successURL, string $failureURL, float $amount, string $productCode = "EPAYTEST", float $taxAmount = 0, float $productDeliveryCharge = 0, float $productServiceCharge = 0)
    {
        // set transaction UUID
        self::$transactionUUID = uniqid() . time();

        // generate signature
        $totalAmount = $taxAmount + $amount + $productDeliveryCharge + $productServiceCharge;
        $generateSignature = new GenerateSignature($totalAmount, $t, self::$secretKey, $productCode);

        // Data's to be send to eSewa
        $postData = [
            "amount"                    => $amount,
            "success_url"               => $successURL,
            "failure_url"               => $failureURL,
            "product_delivery_charge"   => $productDeliveryCharge,
            "product_service_charge"    => $productServiceCharge,
            "product_code"              => $productCode,
            "signature"                 => $generateSignature->get(),
            "signed_field_names"        => "total_amount,transaction_uuid,product_code",
            "tax_amount"                => $taxAmount,
            "total_amount"              => $totalAmount,
            // "transaction_uuid"          => self::$transactionUUID
            "transaction_uuid"          => $t
        ];

        // set url based on environment
        $url = self::$env == "PROD" ? self::$proURL : self::$devURL;

        // Send response through form [Method = POST]
        echo "<form id='esewaForm' action='$url' method='post'>";
        foreach ($postData as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        echo '</form>';
        echo '<script type="text/javascript">document.getElementById("esewaForm").submit();</script>';
    }

    public function validate(string $total_amount, string $transaction_uuid, bool $production = false, string $product_code = 'EPAYTEST')
    {
        if (!$production) {
            $url = "https://uat.esewa.com.np/api/epay/transaction/status/?product_code=$product_code&total_amount=$total_amount&transaction_uuid=$transaction_uuid";
        } else {
            $url = "https://epay.esewa.com.np/api/epay/transaction/status/?product_code=$product_code&total_amount=$total_amount&transaction_uuid=$transaction_uuid";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!$production) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($ch);

        // Check if curl_exec returned false
        // if ($response === false) {
        //     throw new Exception('cURL error: ' . curl_error($ch));
        // }

        curl_close($ch);

        // Decode the JSON response
        $responseArray = json_decode($response, true);


        // Check for JSON decoding errors
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     throw new Exception('JSON decoding error: ' . json_last_error_msg());
        // }

        return $responseArray;
    }
}
