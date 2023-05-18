<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nafezly\Payments\Classes\PaymobWalletPayment;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    private $paymob_api_key;
    private $paymob_wallet_integration_id;
    private $currency;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['CallbackPayment', 'GetCallbackPayment']]);
        $this->paymob_api_key = config('nafezly-payments.PAYMOB_API_KEY');
        $this->currency = config("nafezly-payments.PAYMOB_CURRENCY");
        $this->paymob_wallet_integration_id = config("nafezly-payments.PAYMOB_WALLET_INTEGRATION_ID");
    }
    //payment
    public function payment(Request $request)
    {
        // validate request
        $request->validate([
            'user_first_name' => 'required',
            'user_last_name' => 'required',
            'user_email' => 'required',
            'user_phone' => 'required',
        ]);
        //check if user has orders before
        $orderController = new OrderController();
        $check = $orderController->checkIfUserHasOrdersBerfore($request);

        if ($check) {
            return $check;
        }

        // get user
        $user = Auth::user();

        /*                         Start FirstUrl                              */
        $request_new_token = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/auth/tokens', [
                "api_key" => $this->paymob_api_key
            ])->json();
        /*                         End FirstUrl                              */
        /*                         Start SecondUrl                              */
        //make array of products
        $items = [];
        $total_price = 0;

        foreach ($request->products as $product) {
            $getProduct = Product::find($product['product_id']);
            if ($getProduct->quantity < $product['quantity']) {
                return $this->apiResponse(null, 'الكمية المطلوبة غير متوفرة', 500);
            }

            $total_price += $getProduct->selling_price * $product['quantity'] * 100;
            // array push
            array_push($items, [
                "name" => $getProduct->id,
                "amount_cents" => $getProduct->selling_price * $product['quantity'] * 100,
                "description" => $getProduct->name,
                "quantity" => $product['quantity']
            ]);
        }
        $get_order = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/ecommerce/orders', [
                "auth_token" => $request_new_token['token'],
                "delivery_needed" => "false",
                "amount_cents" => $total_price,
                "items" => $items
            ])->json();
        /*                         End SecondUrl                              */
        /*                         Start ThirdUrl                              */

        $get_url_token = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', [
                "auth_token" => $request_new_token['token'],
                "expiration" => 36000,
                "amount_cents" => $get_order['amount_cents'],
                "order_id" => $get_order['id'],
                "billing_data" => [
                    "apartment" => "NA",
                    "email" => $request->user_email,
                    "floor" => "NA",
                    "first_name" => $request->user_first_name,
                    "street" => $request->customer_address, // user address
                    "building" => "NA",
                    "phone_number" => $request->user_phone,
                    "shipping_method" => "NA",
                    "postal_code" => "NA",
                    "city" => $request->customer_city,
                    "country" => "EGYPT",
                    "last_name" => $request->user_last_name,
                    "extra_description" => "['id' => $user->id]" //send user id to callback function
                ],
                "currency" => $this->currency,
                "integration_id" => $this->paymob_wallet_integration_id,
                'lock_order_when_paid' => true
            ])->json();
        /*                         End ThirdUrl                              */
        /*                         Start FourthUrl                              */
        $get_pay_link = Http::withHeaders(['content-type' => 'application/json'])
            ->post('https://accept.paymob.com/api/acceptance/payments/pay', [
                'source' => [
                    "identifier" => $request->user_phone,
                    'subtype' => "WALLET"
                ],
                "payment_token" => $get_url_token['token']
            ])->json();
        /*                         End FourthUrl                              */

        return $this->apiResponse([
            'payment_id' => $get_order['id'],
            'html' => "",
            'redirect_url' => $get_pay_link['redirect_url']
        ], 'success', 200);
    }

    //save payment
    public function CallbackPayment(Request $request)
    {


        $payloadArray = $request->all();
        $payloadArrayObj = $payloadArray['obj'];


        $amount_cents = (string) $payloadArrayObj['amount_cents'];
        $created_at = (string) $payloadArrayObj['created_at'];
        $currency = (string) $payloadArrayObj['currency'];
        $error_occured = var_export($payloadArrayObj['error_occured'], true);
        $has_parent_transaction = var_export($payloadArrayObj['has_parent_transaction'], true);
        $id = (string) $payloadArrayObj['id'];
        $integration_id = (string) $payloadArrayObj['integration_id'];
        $is_3d_secure = var_export($payloadArrayObj['is_3d_secure'], true);
        $is_auth = var_export($payloadArrayObj['is_auth'], true);
        $is_capture = var_export($payloadArrayObj['is_capture'], true);
        $is_refunded = var_export($payloadArrayObj['is_refunded'], true);
        $is_standalone_payment = var_export($payloadArrayObj['is_standalone_payment'], true);
        $is_voided = var_export($payloadArrayObj['is_voided'], true);
        $order_id = (string) $payloadArrayObj['order']['id'];
        $owner = (string) $payloadArrayObj['owner'];
        $pending = var_export($payloadArrayObj['pending'], true);
        $source_data_pan = (string) $payloadArrayObj['source_data']['pan'];
        $source_data_sub_type = (string) $payloadArrayObj['source_data']['sub_type'];
        $source_data_type = (string) $payloadArrayObj['source_data']['type'];
        $success = var_export($payloadArrayObj['success'], true);


        $ConcatenateString = $amount_cents . $created_at . $currency . $error_occured . $has_parent_transaction . $id . $integration_id . $is_3d_secure . $is_auth . $is_capture . $is_refunded . $is_standalone_payment . $is_voided . $order_id . $owner . $pending . $source_data_pan . $source_data_sub_type . $source_data_type . $success;
        $hashed = hash_hmac('SHA512', $ConcatenateString, config('nafezly-payments.PAYMOB_HMAC'));



        // // Find the position of the start and end of the desired substring
        // $startPos = strpos($request, 'hmac=') + strlen('hmac=');
        // $endPos = strpos($request, ' ', $startPos);

        // // Extract the desired substring
        // $SecureHash = substr($request, $startPos, $endPos - $startPos);

        $hmac = $payloadArray['hmac'];

        //get user id
        $extra_description = $payloadArrayObj['payment_key_claims']['billing_data']['extra_description']; // will return in this format [id => 1]
        //reomve []
        $extra_description = str_replace('[', '', $extra_description);
        $extra_description = str_replace(']', '', $extra_description);
        //split by =>
        $extra_description_array = explode("=>", $extra_description);
        $user_id = $extra_description_array[1];



        if ($hashed == $hmac) {
            try {
                DB::beginTransaction();
                $order = Order::create([
                    'user_id' => $user_id,
                    'customer_name' => $payloadArrayObj['order']['shipping_data']['first_name'] . " " . $payloadArrayObj['order']['shipping_data']['last_name'],
                    'customer_email' => $payloadArrayObj['order']['shipping_data']['email'],
                    'customer_phone' => $payloadArrayObj['order']['shipping_data']['phone_number'],
                    'customer_address' => $payloadArrayObj['order']['shipping_data']['street'],
                    'customer_city' => $payloadArrayObj['order']['shipping_data']['city'],
                    'payment_method' => 'wallet',
                    'order_status' => 'processing',
                    'total_amount' => $payloadArrayObj['amount_cents'] / 100,
                    'paymob_transaction_id' => $payloadArrayObj['id'],
                    'paymob_order_id' => $payloadArrayObj['order']['id'],
                    'paymob_amount_cents' => $payloadArrayObj['amount_cents'],
                    'paymob_pending' => $payloadArrayObj['pending'],
                    'paymob_success' => $payloadArrayObj['success'],
                    'request' => json_encode($request->all()),
                ]);

                foreach ($payloadArrayObj['order']['items'] as $item) {
                    $product = Product::find($item['name']);
                    $product->update([
                        'quantity' => $product->quantity - $item['quantity']
                    ]);

                    // attach product to order
                    $order->products()->attach($product->id, [
                        'quantity' => $item['quantity'],
                        'total_price' => $product->selling_price * $item['quantity'],
                        'created_at' => $payloadArrayObj['created_at'],
                        'updated_at' => $payloadArrayObj['created_at'],
                    ]);
                }

                $orderController = new OrderController();
                $orderController->addBalanceToParentWallet($order);
                $orderController->addPrimaryTransactions($order);

                DB::commit();
                return $this->apiResponse($payloadArrayObj, 'success', 200);
            } catch (\Exception $e) {
                DB::rollback();
                return $this->apiResponse(null, $e->getMessage(), 500);
            }
        } else {
            // create order dammy
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $payloadArrayObj['order']['shipping_data']['first_name'] . " " . $payloadArrayObj['order']['shipping_data']['last_name'],
                'customer_email' => $payloadArrayObj['order']['shipping_data']['email'],
                'customer_phone' => $payloadArrayObj['order']['shipping_data']['phone_number'],
                'customer_address' => $payloadArrayObj['order']['shipping_data']['street'],
                'customer_city' => $payloadArrayObj['order']['shipping_data']['city'],
                'order_details' => 'error in hash',
                'payment_method' => 'wallet',
                'order_status' => 'processing',
                'total_amount' => $payloadArrayObj['amount_cents'] / 100,
                'paymob_transaction_id' => $payloadArrayObj['id'],
                'paymob_order_id' => $payloadArrayObj['order']['id'],
                'paymob_amount_cents' => $payloadArrayObj['amount_cents'],
                'paymob_pending' => $payloadArrayObj['pending'],
                'paymob_success' => $payloadArrayObj['success'],
                'request' => $payloadArrayObj,
            ]);
            return $this->apiResponse($payloadArrayObj, 'error in hash', 500);
        }
    }

    public function GetCallbackPayment(Request $request)
    {
        $paymob = new PaymobWalletPayment();
        $response = $paymob->verify($request);

        return $this->apiResponse($response, 'success', 200);
    }
}
