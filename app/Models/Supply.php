<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    protected $table  = 'supplier';
    protected $fillable = [
        'supplier_name',
        'supplier_telephone',
        'supplier_address'
    ];
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
