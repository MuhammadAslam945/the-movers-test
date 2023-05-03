<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Exception;
use App\Base\Constants\Setting\Settings;

trait JazzcashTrait {
    private $apiUrl = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction';

    private $merchantId = 'YOUR_MERCHANT_ID_HERE';
    private $password = 'YOUR_PASSWORD_HERE';
    private $secureHashSalt = 'YOUR_SECURE_HASH_SALT_HERE';

    private function generateRequestId() {
        return 'JC' . Carbon::now()->format('YmdHis') . Str::random(6);
    }

    private function generateSecureHash(array $data) {
        $secureHash = $this->merchantId . $data['pp_TxnRefNo'] . $data['pp_Amount'] . $data['pp_TxnCurrency'] . $data['pp_TxnDateTime'] . $this->secureHashSalt;

        return hash('sha256', $secureHash);
    }

    public function initiatePayment(array $data) {
        $payload = [
            'pp_Version' => '1.1',
            'pp_TxnType' => 'MWALLET',
            'pp_Language' => 'EN',
            'pp_MerchantID' => $this->merchantId,
            'pp_SubMerchantID' => '',
            'pp_Password' => $this->password,
            'pp_BankID' => 'TBANK',
            'pp_ProductID' => 'RETL',
            'pp_TxnRefNo' => $data['order_id'],
            'pp_Amount' => $data['order_amount'],
            'pp_TxnCurrency' => $data['order_currency'],
            'pp_TxnDateTime' => Carbon::now()->format('YmdHis'),
            'pp_BillReference' => 'Jazzcash Payment '.$data['order_id'],
            'pp_Description' => $data['description'],
            'pp_TxnExpiryDateTime' => Carbon::now()->addMinutes(20)->format('YmdHis'),
            'pp_ReturnURL' => $data['return_url'],
            'ppmpf_1' => $data['phone'],
            'ppmpf_2' => $data['user_id'],
            'ppmpf_3' => $data['order_id'],
            'ppmpf_4' => $data['order_amount'],
            'ppmpf_5' => $data['description'],
        ];

        $payload['pp_SecureHash'] = $this->generateSecureHash($payload);

        $response = Http::asForm()->timeout(180)->post($this->apiUrl, $payload);

        return $response->json();
    }
}

