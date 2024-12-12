<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    protected $fillable = [
        'code_member',
        'name_member',
        'address',
        'telephone'
    ];
}
