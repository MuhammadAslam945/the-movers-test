<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Base\Constants\Auth\Role;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Traits\JazzCashTrait;
use App\Models\User;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\UserWallet;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Jobs\Notifications\SendPushNotification;
use App\Base\Constants\Setting\Settings;
use Illuminate\Http\Request;

class jazzcashUserController  extends Controller
{

    use JazzCashTrait;
    public function index()
    {
        return view('addfunds');
    }

    public function addTransaction(Request $request)
    {
        // dd($request);
        try {
            $userId =5; // auth()->user()->id;
            $orderAmount = $request->order_amount;
            $phone = $request->phone;
            $cnic = $request->cnic;
            $secretKey = get_settings(Settings::JAZZCASH_TEST_API_URL);

            $orderId = Carbon::now()->timestamp . $userId;
            $description = 'The Movers Cab';
            $transactionId = "T" . date('YmdHis');

            if (get_settings(Settings::JAZZCASH_ENVIRONMENT) == 'test') {
                $jazzcash_environment = true;
            } else {
                $jazzcash_environment = false;
            }

            $response = $this->initiatePayment([
                'order_id' => $orderId,
                'order_amount' => $orderAmount,
                'order_currency' => 'PKR',
                'phone' => $phone,
                'cnic' => $cnic,
                'user_id' => $userId,
                'transaction_id' => $transactionId,
                'description' => $description,
                'api_url' => $secretKey,
                'user' => $userId // auth()->user(),
            ]);
        ////     echo '<pre>';
           // var_dump($response);
          //  echo '</pre>';
            // die();
            // 03123456789 | 345678
            if ($response['pp_ResponseCode'] === '000') {
                $transactionId = $response['pp_TxnRefNo'];
                $amountAdded = $response['ppmpf_4'];

                $userWalletHistory = UserWalletHistory::create([
                    'user_id' => $response['ppmpf_2'], 
                    'transaction_id' => $response['pp_TxnRefNo'], 
                    'amount' => $response['pp_Amount'], 
                    'remarks' => WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET, 
                    'is_credit' => true
                ]);
                
                $userWallet = UserWallet::where('user_id', $userId)->first();

                if ($userWallet) {
                    $userWallet->update([
                        'amount_added' => $userWallet->amount_added + $amountAdded,
                        'amount_balance' => $userWallet->amount_balance + $amountAdded,
                    ]);
                } else {
                    UserWallet::create([
                        'user_id' => $userId,
                        'amount_added' => $amountAdded,
                        'amount_balance' => $amountAdded,
                        'amount_spent' => 0,
                    ]);
                }

                session()->flash('success', [
                    'type' => 'success',
                    'code' => 200, // Replace this with your desired error code
                    'text' => 'Transaction successfull.',
                ]);
                return view('jazzcash');
            } else {
                session()->flash('error', [
                    'type' => 'error',
                    'code' => 500, // Replace this with your desired error code
                    'text' => 'An error occurred.',
                ]);
                return view('error');
            }
        } catch (\Throwable $th) {
            throw $th;
            die();
        }
    }

    public function error()
    {
        return view('error');
    }
}
