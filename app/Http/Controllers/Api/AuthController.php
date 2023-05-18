<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Termwind\Components\Dd;

class AuthController extends Controller
{
    use ApiResponseTrait;
    protected $guard;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->guard = "api";
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }
        if (!$token = auth($this->guard)->attempt($validator->validated())) {
            return $this->apiResponse(null, 'البيانات المدخلة غير صحيحة', 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();
        try {
            // image
            $image = $request->file('image');
            if ($image) {
                $image_name = $request->name . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $image_name);
            } else {
                $image_name = null;
            }
            // create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => bcrypt($request->password),
                'address' => $request->address,
                'city' => $request->city,
                'image' => $image_name,
            ]);




            DB::commit();
            return $this->createNewToken(auth($this->guard)->login($user));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function updateProfile(Request $request)
    {
        
        $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            // 'password' => 'string|confirmed|min:6',
            'phone_number' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // image
            $image = $request->file('image');
            if ($image) {
                $image_name = $request->name . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/users'), $image_name);
            }

            // check if user has password and confirm password and previous password
            if ($request->password && $request->password_confirmation && $request->old_password) {
                if (Hash::check($request->old_password, $user->password)) {
                    if ($request->password == $request->password_confirmation) {
                        $user->update([
                            'password' => bcrypt($request->password),
                        ]);
                    } else {
                        return $this->apiResponse(null, 'كلمة المرور الجديدة غير متطابقة', 422);
                    }
                } else {
                    return $this->apiResponse(null, 'كلمة المرور السابقة غير صحيحة', 422);
                }
            }

            // create user
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,

                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'city' => $request->city,
                'image' => $image_name ?? $user->image,
            ]);

            DB::commit();
            return $this->apiResponse(new UserResource($user), 'تم تحديث البيانات بنجاح', 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth($this->guard)->logout();
        return $this->apiResponse(null, 'تم تسجيل الخروج بنجاح', 200);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth($this->guard)->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return $this->apiResponse(new UserResource(auth($this->guard)->user()), null, 200);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $user = new UserResource(auth($this->guard)->user());
        return $this->apiResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60 * 24
        ], null, 201);
    }
}
