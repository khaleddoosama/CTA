<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $Tree;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->Tree = 0;
    }
    public function index()
    {
        // get the current user id
        $user_id = Auth::id();
        // get the order of the current user using order resource
        $order = OrderResource::collection(Order::where('user_id', $user_id)->get());
        // return a response
        return $this->apiResponse($order, 'نجاح', 200);
    }


    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'customer_address' => 'required',
            'customer_city' => 'required',
            'payment_method' => 'required',
            'total_amount' => 'numeric',
            'discount' => 'numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|numeric|exists:products,id',
            'products.*.quantity' => 'required|numeric',
        ]);
        // if the validation fails
        if ($validator->fails()) {
            // return a response
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }
        DB::beginTransaction();
        try {
            // get the current user 
            $user = Auth::user();

            // check if the user has orders berfore or not
            $check = $this->checkIfUserHasOrdersBerfore($request);
            if ($check) {
                return $check;
            }

            // create a new order
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'customer_city' => $request->customer_city,
                'order_details' => $request->order_details,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount,
                'order_status' => 'processing',
            ]);
            // create a new order product
            foreach ($request->products as $product) {
                $getProduct = Product::find($product['product_id']);
                if ($getProduct->quantity < $product['quantity']) {
                    return $this->apiResponse('null', 'الكمية المطلوبة غير متوفرة', 400);
                }
                // update product quantity
                $getProduct->quantity = $getProduct->quantity - $product['quantity'];
                $getProduct->save();
                // get the product price
                $price = $getProduct->selling_price;
                if ($getProduct->discount_price) {
                    $price = $getProduct->selling_price - ($getProduct->selling_price * $getProduct->discount_price / 100);
                }
                $order->total_amount += $price * $product['quantity'];
                $order->save();


                $order->products()->attach(
                    $product['product_id'],
                    [
                        'quantity' => $product['quantity'],
                        'total_price' => $price * $product['quantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
            // update the total amount if there is a discount
            $order->total_amount = $order->total_amount - ($order->total_amount * $order->discount / 100);
            $order->save();

            // add balance to parent wallet
            $this->addBalanceToParentWallet($order);


            // add Primary transactions
            $this->addPrimaryTransactions($order);

            DB::commit();
            // return a response
            return $this->apiResponse(new OrderResource($order), 'تم اضافة الطلب بنجاح', 201);
        }
        // if there is an error
        catch (\Exception $e) {

            DB::rollback();
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    // add balace to parent wallet
    public function addBalanceToParentWallet($order)
    {
        // get the user
        $user = $order->user;
        // get the parent
        $parent = User::where('id', $user->parent_id)->first();

        // Get the tree funds
        $tree_funds = (0.25 * $order->total_amount);


        // check parent network type
        if ($parent->network_type == 'basic') {

            // update parent balance
            $parent->wallet->transactions()->create([
                'from_user_id' => $user->id,
                'type' => 'credit',
                'amount' => $tree_funds * 0.1,
                'description' => 'Direct Child',
                'status' => 'pending',
                'order_id' => $order->id,
            ]);
            $this->Tree += $tree_funds * 0.1;

            // update grandparent balance
            if ($parent->parent_id) {
                $grandparent = User::find($parent->parent_id);
                // check grandparent network type
                if ($grandparent->network_type == 'basic') {
                    // check if the grandparent has 2 childs or less
                    $ParentsAndUncles = User::where('parent_id', $grandparent->id)->get();
                    $BrothersAndCousins_Count = 0;
                    foreach ($ParentsAndUncles as $ParentUncle) {
                        $BrothersAndCousins_Count += User::where('parent_id', $ParentUncle->id)->count();
                    }

                    if ($BrothersAndCousins_Count <= 2) {
                        $grandparent->wallet->transactions()->create([
                            'wallet_id' => $grandparent->wallet->id,
                            'from_user_id' => $user->id,
                            'type' => 'credit',
                            'amount' => $tree_funds * 0.05,
                            'description' => 'Direct GrandChild',
                            'status' => 'pending',
                            'order_id' => $order->id,
                        ]);
                        $this->Tree += $tree_funds * 0.05;
                    }

                    // check if the grandparent has become advanced
                    $this->checkIfUserBecomeAdvanced($grandparent);
                    $this->updateAllGrandgrandparentsBalance($grandparent, $order);
                } else {
                    // get first three childs under grandparent
                    $grandparent_childs = User::where('parent_id', $grandparent->id)->take(3)->get();
                    // check if the parent is in the first three childs
                    if (!$grandparent_childs->contains($parent)) {
                        $grandparent->wallet->transactions()->create([
                            'wallet_id' => $grandparent->wallet->id,
                            'from_user_id' => $user->id,
                            'type' => 'credit',
                            'amount' => $tree_funds * 0.85,
                            'description' => 'Advanced Network',
                            'status' => 'pending',
                            'order_id' => $order->id,
                        ]);
                        $this->Tree += $tree_funds * 0.85;
                    } else {

                        $this->updateAllGrandgrandparentsBalance($grandparent, $order);
                    }
                }
            }
            $this->checkIfUserBecomeAdvanced($parent);
        } else {
            $parent->wallet->transactions()->create([
                'wallet_id' => $parent->wallet->id,
                'from_user_id' => $user->id,
                'type' => 'credit',
                'amount' => $tree_funds * 0.85,
                'description' => 'Advanced Network',
                'status' => 'pending',
                'order_id' => $order->id,
            ]);
            $this->Tree += $tree_funds * 0.85;
        }
        return true;
    }

    // add Primary transactions
    public function addPrimaryTransactions($order)
    {
        // transaction for order price
        $order->transactions()->create([
            'wallet_id' => $order->user->wallet->id,
            'from_user_id' => $order->user->id,
            'amount' => $order->total_amount,
            'type' => 'debit',
            'description' => 'Order #' . $order->id . ' price',
            'status' => 'pending',
        ]);

        // transaction for perfume price have 65%
        $order->transactions()->create([
            'wallet_id' => 1,
            'from_user_id' => $order->user->id,
            'amount' => $order->total_amount * 0.65,
            'type' => 'credit',
            'description' => 'perfume price',
            'status' => 'pending',
        ]);

        // transaction for employees earning have 10%
        $order->transactions()->create([
            'wallet_id' => 2,
            'from_user_id' => $order->user->id,
            'amount' => $order->total_amount * 0.1,
            'type' => 'credit',
            'description' => 'employess',
            'status' => 'pending',
        ]);

        // transaction for Tree have 25%
        $order->transactions()->create([
            'wallet_id' => 3,
            'from_user_id' => $order->user->id,
            'type' => 'credit',
            'amount' => (0.25 * $order->total_amount) - $this->Tree,
            'description' => 'remaining Tree',
            'status' => 'pending',
        ]);
    }

    // check if the user has orders berfore or not
    public function checkIfUserHasOrdersBerfore($request)
    {
        // get the current user
        $user = Auth::user();

        $checkOrder = Order::where('user_id', $user->id)->where('order_status', '!=', 'canceled')->first();
        // if the user has orders berfore
        if (!$checkOrder) {
            $validator = Validator::make($request->all(), [
                'parent_code' => 'required|exists:users,code',
            ]);
            // if the validation fails
            if ($validator->fails()) {
                // return a response
                return $this->apiResponse(null, $validator->errors()->first(), 400);
            }
            $parent = User::where('code', $request->parent_code)->first();
            $checkIfParentCanAppendUser = $this->checkIfParentCanAppendUser($parent);
            if ($checkIfParentCanAppendUser !== true) {
                return $this->apiResponse(null, $checkIfParentCanAppendUser, 400);
            }

            // update user
            $user->update([
                'parent_id' => $parent->id,
                'status' => 'active',
            ]);
        }
    }

    // check if the user become advanced
    // the user become advanced if he has 3 childs and 2 grandchilds
    public function checkIfUserBecomeAdvanced($user)
    {
        $childs = User::where('parent_id', $user->id)->get();
        $childs_count = $childs->count();
        $grandchild_Count = 0;
        foreach ($childs as $child) {
            $grandchild_Count += User::where('parent_id', $child->id)->count();
        }
        if ($childs_count >= 3 && $grandchild_Count >= 2) {
            $user->update([
                'network_type' => 'advanced',
            ]);
            return true;
        }
        return false;
    }

    // check if the parent can append the user
    public function checkIfParentCanAppendUser($parent)
    {
        $childs = User::where('parent_id', $parent->id)->get();
        $childs_count = $childs->count();
        if ($childs_count == 6) {
            return 'عدد الاطفال مكتمل';
        }
        if ($parent->network_type == 'basic' && $childs_count == 3) {
            return 'لا يمكنك اضافة اطفال اخرين حتي تتغير حاله الاب الي متقدم';
        }

        return true;
    }

    // updat the advanced grandgrandparent balance
    public function updateAllGrandgrandparentsBalance($grandparent, $order)
    {
        // Get the tree funds
        $tree_funds = (0.25 * $order->total_amount);


        while ($grandparent->parent_id) {
            $parent = $grandparent;
            $grandparent = User::find($grandparent->parent_id);
            // check grandparent network type
            if ($grandparent->network_type != 'basic') {
                // get first three childs under grandparent
                $grandparent_childs = User::where('parent_id', $grandparent->id)->take(3)->get();
                // check if the parent is in the first three childs
                if (!$grandparent_childs->contains($parent)) {
                    $grandparent->wallet->transactions()->create([
                        'wallet_id' => $grandparent->wallet->id,
                        'from_user_id' => Auth::id(),
                        'type' => 'credit',
                        'amount' => $tree_funds * 0.85,
                        'description' => 'Advanced Network',
                        'status' => 'pending',
                        'order_id' => $order->id,
                    ]);
                    $this->Tree += $tree_funds * 0.85;
                    return true;
                }
            }
        }
        return false;
    }


    public function complete(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }

        try {
            // begin a transaction
            DB::beginTransaction();
            // get the current user id
            $user = Auth::user();
            // get the order
            $order = Order::where('user_id', $user->id)->where('id', $id)->where('order_status', 'processing')->first();
            // if the order doesn't exist
            if (!$order) {
                // return a response
                return $this->apiResponse(null, 'الطلب غير موجود', 404);
            }
            // add code if the dont have
            if (!$user->code) {
                $user->update([
                    'code' => $user->generateUserCode(),
                ]);
            }

            // change the status of all transactions to completed
            $transactions = Transaction::where('from_user_id', $user->id)->where('status', 'pending')->where('order_id', $id)->get();

            foreach ($transactions as $transaction) {
                if ($transaction->type == 'credit') {
                    $transaction->wallet->update([
                        'balance' => $transaction->wallet->balance + $transaction->amount,
                    ]);
                }
            }

            // update the order status
            $order->update([
                'order_status' => 'completed',
            ]);

            // commit the transaction
            DB::commit();
            // return a response
            return $this->apiResponse(null, 'تم اكمال الطلب بنجاح', 200);
        }
        // if there is an error
        catch (\Exception $e) {
            DB::rollback();
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }


    public function destroy(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }
        try {
            // begin a transaction
            DB::beginTransaction();
            // get the current user id
            $user = Auth::user();
            // get the order
            $order = Order::where('user_id', $user->id)->where('id', $id)->where('order_status', 'processing')->first();
            // if the order doesn't exist
            if (!$order) {
                // return a response
                return $this->apiResponse(null, 'الطلب غير موجود', 404);
            }

            // update the order status
            $order->update([
                'order_status' => 'canceled',
            ]);

            // change the status of all transactions to canceled
            // Transaction::where('from_user_id', $user->id)->where('status', 'pending')->where('order_id', $id)->update([
            //     'status' => 'canceled',
            // ]);

            // return all orders count for user 
            $ordersUserCount = Order::where('user_id', $user->id)->where('order_status', '!=', 'canceled')->count();
            // if the user has no orders
            if ($ordersUserCount == 0) {
                // update the user status
                $user->update([
                    'parent_id' => null,
                    'status' => 'inactive',
                ]);
            }

            // reutrn a product quantity
            foreach ($order->products as $product) {
                $getProduct = Product::find($product->id);
                $getProduct->quantity = $getProduct->quantity + $product->pivot->quantity;
                $getProduct->save();
            }


            // commit the transaction
            DB::commit();
            // return a response
            return $this->apiResponse(null, 'تم حذف الطلب بنجاح', 200);
        } catch (\Exception $e) {
            DB::rollback();
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }
}
