<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'member_id',
        'user_id',
        'total_item',
        'total_price',
        'discount',
        'accepted'
    ];
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
