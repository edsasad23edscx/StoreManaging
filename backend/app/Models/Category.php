<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}