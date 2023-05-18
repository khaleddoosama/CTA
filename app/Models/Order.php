<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order__products', 'order_id', 'product_id')->withPivot('quantity', 'total_price', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
   
    public function save(array $options = [])
    {
        // Determine if order_status has changed
        $originalOrderStatus = $this->getOriginal('order_status');
        $newOrderStatus = $this->getAttribute('order_status');

        if ($originalOrderStatus !== $newOrderStatus) {
            // Get related transactions
            $transactions = $this->transactions;

            // Update transaction status
            $transactions->each(function ($transaction) use ($newOrderStatus) {
                $transaction->update([
                    'status' => $newOrderStatus,
                ]);
            });
        }

        return parent::save($options);
    }
}
