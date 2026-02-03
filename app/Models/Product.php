<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'name',
        'sku',
        'category',
        'price',
        'stock',
        'status',
        'image',           // ⬅️ CAMBIO A image
        'image_public_id', // ⬅️ CAMBIO A image_public_id
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $hidden = [
        'image_public_id', // Ocultar public_id en respuestas JSON
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
