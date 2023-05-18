<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class WishlistController extends Controller
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
        // get the wishlist of the current user using wishlist resource
        $wishlist = WishlistResource::collection(Wishlist::where('user_id', $user_id)->get());
        // return a response
        return $this->apiResponse($wishlist, 'Success', 200);

    }

    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric|exists:products,id'
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
            // check if the product is already in the wishlist
            $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();
            // if the product is already in the wishlist
            if ($wishlist) {
                // return a response
                return $this->apiResponse(null, 'هذا المنتج موجود بالفعل في المفضلة', 400);
            }
            // if the product is not in the wishlist
            else {
                // create a new wishlist
                Wishlist::create([
                    'user_id' => $user_id,
                    'product_id' => $product_id
                ]);
                // return a response
                return $this->apiResponse(null, 'تمت الإضافة إلى المفضلة', 200);
            }
        }
        // if an error occured
        catch (\Exception $e) {
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }
 
    public function destroy(string $id)
    {
        // get the current user id
        try {
            $user_id = auth()->user()->id;
            // get the product id from the request
            $product_id = $id;
            // check if the product is in the wishlist
            $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();
            // if the product is in the wishlist
            if ($wishlist) {
                // delete the product from the wishlist
                $wishlist->delete();
                // return a response
                return $this->apiResponse(null, 'تمت الإزالة من المفضلة', 200);
            }
            // if the product is not in the wishlist
            else {
                // return a response
                return $this->apiResponse(null, 'هذا المنتج غير موجود في المفضلة', 400);
            }
        }
        // if an error occured
        catch (\Exception $e) {
            // return a response
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }
}
