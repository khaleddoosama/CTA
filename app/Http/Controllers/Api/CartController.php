<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{
    use ApiResponseTrait;
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        // get the current user id
        $user_id = auth()->user()->id;
        // get the cart of the current user using cart resource
        $cart = CartResource::collection(Cart::where('user_id', $user_id)->get());
        // return a response
        return $this->apiResponse($cart, 'نجاح', 200);
    }

    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric|exists:products,id',
            'quantity' => 'required|numeric|min:1'
        ]);
        // if the validation fails
        if ($validator->fails()) {
            // return a response
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }
        // get the current user id
        try {
            $user_id = auth()->user()->id;
            // get the product id from the request
            $product_id = $request->product_id;
            // get the quantity from the request
            $quantity = $request->quantity;
            // check if the product is already in the cart
            $cart = Cart::where('user_id', $user_id)->where('product_id', $product_id)->first();
            // if the product is already in the cart
            if ($cart) {
                // return a response
                return $this->apiResponse(null, 'هذا المنتج موجود بالفعل في السلة', 400);
            }
            // if the product is not in the cart
            else {
                // get the product
                $product = Product::find($product_id);
                // if the product is not available
                if (!$product->status) {
                    // return a response
                    return $this->apiResponse(null, 'هذا المنتج غير متوفر', 400);
                }
                // if the product is available
                // calculate the total price
                $total_price = $product->selling_price * $quantity;

                // create a new cart
                Cart::create([
                    'user_id' => $user_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                ]);
                // return a response
                return $this->apiResponse(null, 'تمت الاضافة بنجاح', 200);
            }
        }
        // if there is an error
        catch (\Exception $e) {
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }

    public function update(Request $request, string $id)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:1'
        ]);
        // if the validation fails
        if ($validator->fails()) {
            // return a response
            return $this->apiResponse(null, $validator->errors()->first(), 400);
        }
        // get the current user id
        try {
            $user_id = auth()->user()->id;
            // get the quantity from the request
            $quantity = $request->quantity;
            // get the cart
            $cart = Cart::where('user_id', $user_id)->where('id', $id)->first();
            // if the cart doesn't exist
            if (!$cart) {
                // return a response
                return $this->apiResponse(null, 'السلة غير موجودة', 404);
            }
            // if the cart exists
            // get the product
            $product = Product::find($cart->product_id);
            // if the product is not available
            if (!$product->status) {
                // return a response
                return $this->apiResponse(null, 'هذا المنتج غير متوفر', 400);
            }
            // if the product is available
            // calculate the total price
            $total_price = $product->selling_price * $quantity;
            // update the cart
            $cart->update([
                'quantity' => $quantity,
                'total_price' => $total_price,
            ]);
            // return a response
            return $this->apiResponse(null, 'تم تعديل السلة بنجاح', 200);
        }
        // if there is an error
        catch (\Exception $e) {
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        // get the current user id
        try {
            $user_id = auth()->user()->id;
            // get the cart
            $cart = Cart::where('user_id', $user_id)->where('id', $id)->first();
            // if the cart is not found
            if (!$cart) {
                // return a response
                return $this->apiResponse(null, 'هذا المنتج غير موجود في السلة', 400);
            }
            // if the cart is found
            else {
                // delete the cart
                $cart->delete();
                // return a response
                return $this->apiResponse(null, 'تمت الازالة بنجاح', 200);
            }
        }
        // if there is an error
        catch (\Exception $e) {
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }
}
