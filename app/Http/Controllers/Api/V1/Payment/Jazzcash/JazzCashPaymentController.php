<?php

namespace App\Http\Controllers\Api\V1\Payment\Jazzcash;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Transformers\Payment\UserWalletHistoryTransformer;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use Illuminate\Support\Facades\Log;
use App\Models\Payment\DriverWallet;
use App\Http\Requests\Payment\AddBeneficary;
use App\Base\Constants\Masters\WalletRemarks;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Http\Requests\Payment\TransferToBankAccount;
use App\Http\Requests\Payment\GenerateCfTokenRequest;
use App\Traits\JazzCashTrait;
use App\Base\Constants\Setting\Settings;
use App\Models\User;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\NotifyViaMqtt;
use App\Models\Payment\UserWallet;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Jobs\Notifications\SendPushNotification;

class JazzCashPaymentController extends Controller
{
    use JazzCashTrait;


    public function generateCfToken(GenerateCfTokenRequest $request)
    {
        $jazz_cash_trait = new JazzCashTrait();

        $orderId = env('APP_NAME').'-'.Carbon::now()->timestamp.'---'.auth()->user()->id;
        $data = [
            'order_id' => $orderId,
            'order_amount' => $request->order_amount,
            'order_currency' => $request->order_currency,
            'phone' => $request->phone,
            'user_id' => auth()->user()->id,
            'return_url'=>'http://127.0.0.1:8000/admin/add/payment/' . $user->id,
            'description' => 'The Movers Cab',
        ];

        $response = $jazz_cash_trait->initiatePayment($data);

        if (isset($response['pp_SecureHash'])) {
            return response()->json([
                'success' => true,
                'orderId' => $orderId,
                'pp_SecureHash' => $response['pp_SecureHash'],
                'pp_SessionId' => $response['pp_SessionId'],
                'pp_MerchantID' => $this->merchantId,
                'pp_ReturnURL' => '',
                'pp_TxnRefNo' => $response['pp_TxnRefNo'],
                'pp_Amount' => $request->order_amount,
                'pp_TxnCurrency' => $request->order_currency
            ]);
        }

        return $this->respondFailed();
    }



    public function token()
    {
        $token =  $this->getToken();

        return response()->json(['success'=>true,'message'=>'token_generated','token'=>$token
       ]);
    }


    public function addBeneficary(AddBeneficary $request)
    {
        $beneficiary = $request->all();
        $beneficiary['name'] = auth()->user()->name;
        $beneficiary['email'] = (auth()->user()->email)?auth()->user()->email:generateRandomEmail();
        $beneficiary['phone'] = auth()->user()->mobile;

        // https://dev.cashfree.com/bank-account-verification/bank-validation/testing

        // $beneficiary = $request->all();
        // $beneficiary['name'] = 'vicky';
        // $beneficiary['email'] = generateRandomEmail();
        // $beneficiary['phone'] = '9361380603';

        $beneId = $this->addBeneficiary($beneficiary);

        $beneficiaryArray = $request->all();
        $beneficiaryArray['beneId'] = $beneId;
        $beneficiaryArray['user_id'] = auth()->user()->id;

        Benefits::create($beneficiaryArray);

        return response()->json(['success'=>true,'message'=>'beneficary_created_successfully']);
    }

    /**
    * Get beneficiary
    * @hideFromAPIDocumentation
    *
    */
    public function getBeneficary()
    {
        $beneficiary = Benefits::where('user_id', auth()->user()->id)->first();

        return response()->json(['success'=>true,'message'=>'beneficary_list','beneficary'=>$beneficiary ]);
    }


    /**
     * @bodyParam orderId string required order id of the request
     * @bodyParam orderAmount double required order amount of the request
     * @bodyParam referenceId string required reference id of the request
     * @bodyParam txStatus string required txStatus of the request
     * @bodyParam paymentMode string required paymentMode of the request
     * @bodyParam txMsg string required txMsg of the request
     * @bodyParam txTime string required txTime of the request
     * @bodyParam signature string required signature of the request
     *
     * */
    public function addTowalletwebHooks(Request $request)
    {
        try {
            $response = $request->all();
            Log::info($response);

            // TODO: Add Jazzcash specific implementation here

            // Retrieve user based on order ID
            $orderId = $request->orderId;
            $id = explode('---', $orderId);
            $user = User::find($id[1]);

            // Check if user has role of user, driver or owner and create wallet accordingly
            if ($user->hasRole('user')) {
                $wallet_model = new UserWallet();
                $wallet_add_history_model = new UserWalletHistory();
                $user_id = $user->id;
            } elseif ($user->hasRole('driver')) {
                $wallet_model = new DriverWallet();
                $wallet_add_history_model = new DriverWalletHistory();
                $user_id = $user->driver->id;
            } else {
                $wallet_model = new OwnerWallet();
                $wallet_add_history_model = new OwnerWalletHistory();
                $user_id = $user->owner->id;
            }

            // Add amount to user's wallet
            $orderAmount = $request->orderAmount;
            $user_wallet = $wallet_model::firstOrCreate(['user_id' => $user_id]);
            $user_wallet->amount_added += $orderAmount;
            $user_wallet->amount_balance += $orderAmount;
            $user_wallet->save();
            $user_wallet->fresh();

            // Add transaction history to user's wallet
            $wallet_add_history_model::create([
                'user_id' => $user_id,
                'card_id' => null,
                'amount' => $orderAmount,
                'transaction_id' => $request->orderId,
                'conversion' => null,
                'merchant' => null,
                'remarks' => WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
                'is_credit' => true
            ]);

            // Push notification to user
            $title = trans('push_notifications.amount_credited_to_your_wallet_title', [], $user->lang);
            $body = trans('push_notifications.amount_credited_to_your_wallet_body', [], $user->lang);
            dispatch(new SendPushNotification($user, $title, $body));

            // Return response
            if (access()->hasRole(Role::USER)) {
                $result = fractal($user_wallet, new WalletTransformer);
            } elseif (access()->hasRole(Role::DRIVER)) {
                $result = fractal($user_wallet, new DriverWalletTransformer);
            } else {
                $result = fractal($user_wallet, new OwnerWalletTransformer);
            }
            return $this->respondSuccess($result, 'money_added_successfully');

        } catch (\Exception $e) {
            Log::error($e);
            Log::error('Error while Add money to wallet. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
    }

}
