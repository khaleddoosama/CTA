<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    use ApiResponseTrait;
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        $wallet = auth()->user()->wallet;
        return $this->apiResponse(new WalletResource($wallet), null, 200);
    }

    public function deposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }
        DB::beginTransaction();
        try {
            $wallet = auth()->user()->wallet;
            $wallet->update([
                'balance' => $wallet->balance + $request->amount,
            ]);
            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'deposit',
            ]);
            DB::commit();
            return $this->apiResponse($wallet, 'تم إيداع المبلغ بنجاح', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }
        DB::beginTransaction();
        try {
            $wallet = auth()->user()->wallet;
            if ($wallet->balance < $request->amount) {
                return $this->apiResponse(null, 'ليس لديك رصيد كافي', 422);
            }
            $wallet->update([
                'balance' => $wallet->balance - $request->amount,
            ]);
            Transaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->amount,
                'type' => 'withdraw',
            ]);
            DB::commit();
            return $this->apiResponse($wallet, 'تم سحب المبلغ بنجاح', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }


    
}
