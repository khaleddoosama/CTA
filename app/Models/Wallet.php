<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::updated(function ($wallet) {
    //         $type = $wallet->getOriginal('balance') > $wallet->balance ? 'debit' : 'credit';
    //         $amount = abs($wallet->getOriginal('balance') - $wallet->balance);
    //         if ($amount == 5) {
    //             $description = 'Direct Child';
    //         } elseif ($amount == 2.5) {
    //             $description = 'Direct GrandChild';
    //         } elseif ($amount == 42.5) {
    //             $description = 'Advanced Network';
    //         } elseif ($amount == 200) {
    //             $description = 'buying a product';
    //         }
    //         Transaction::create([
    //             'from_user_id' => -1,
    //             'wallet_id' => $wallet->id,
    //             'type' => $type,
    //             'amount' => $amount,
    //             'description' => $description ?? null,
    //         ]);
    //     });
    // }
}
