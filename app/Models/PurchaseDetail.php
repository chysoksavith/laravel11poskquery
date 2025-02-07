<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchase_details';
    protected $fillable = [
        'product_id',
        'purchase_id',
        'purchase_price',
        'amount',
        'subtotal'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
