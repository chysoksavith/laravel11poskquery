<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $table = 'members';
    protected $fillable = [
        'code_member',
        'name_member',
        'address',
        'telephone'
    ];
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
