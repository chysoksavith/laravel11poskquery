<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $table = "purchases";
    protected $fillable = [
        'supplier_id',
        'total_item',
        'total_price',
        'discount'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
