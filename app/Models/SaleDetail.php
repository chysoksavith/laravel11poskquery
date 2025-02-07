<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = 'sale_details';
    protected $fillable = [
        'sale_id',
        'product_id',
        'selling_price',
        'amount',
        'discount',
        'subtotal'
    ];
}
