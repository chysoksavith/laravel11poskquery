<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'category_name',
        'sug'
    ];

    // auto make slug
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($category) {
            $category->slug = Str::slug($category->category_name);
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
