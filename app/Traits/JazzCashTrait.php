<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
trait JazzcashTrait
{
    private $apiUrl = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/domwallettransaction';
    private $merchantId = 'MC58603';
    private $password = '329g82w24z';
    private $salt = 'zut5170cyv';

    public function initiatePayment(array $data)
    {
        $url = $this->apiUrl;
        $payload = [
            'pp_Language' => 'EN',
            'pp_MerchantID' => $this->merchantId,
            'pp_SubMerchantID' => '',
            'pp_Password' => $this->password,
            'pp_TxnRefNo' => $data['transaction_id'],
            'pp_MobileNumber' =>$data['phone'],
            "pp_CNIC" =>$data['cnic'],
            'pp_Amount' => $data['amount'],
            "pp_DiscountedAmount"=>'',
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => date('YmdHis'),
            'pp_BillReference' => $data['order_id'],
            'pp_Description' => $data['description'],
            'pp_TxnExpiryDateTime' => date('YmdHis', strtotime('+2 days')),
            'pp_SecureHash' => '',
            'ppmpf_1' => $data['phone'],
            'ppmpf_2' => $data['user_id'],
            'ppmpf_3' => $data['order_id'],
            'ppmpf_4' => $data['order_amount'],
            'ppmpf_5' => $data['description'],
        ];

        $pp_SecureHash = $this->get_SecureHash($payload);
        $payload['pp_SecureHash'] = $pp_SecureHash;
        $response = Http::post($url, $payload);
        return $response->json();
    }
    private function get_SecureHash($data_array)
    {
        ksort($data_array);

        $str = '';
        foreach ($data_array as $key => $value) {
            if (!empty($value)) {
                $str .= '&' . $value;
            }
        }

        $str = $this->salt . $str;

        $pp_SecureHash = hash_hmac('sha256', $str, $this->salt);

        return $pp_SecureHash;
    }
}
