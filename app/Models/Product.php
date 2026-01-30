<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // se crea este fillabe para ver que campos pueden ser alterdos
    protected $fillable = [
        'user_id',
        'name',
        'sku',
        'category',
        'price',
        'stock',
        'status',
        'avatar',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
