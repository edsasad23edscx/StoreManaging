<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'price',
        'stock_quantity',
        'min_stock_level',
        'image_path',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Product belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}