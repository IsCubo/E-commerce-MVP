<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'is_offer',
        'is_active',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    protected $appends = ['whatsapp_url'];

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getWhatsappUrlAttribute(): string
    {
        $settings = Setting::cached();
        $number = $settings->get('whatsapp_number');
        $baseMessage = $settings->get('welcome_message');

        if (!$number) return '#';

        $message = $baseMessage . " " . $this->name . " (Precio: $" . number_format($this->discount_price ?? $this->price, 2) . ")";
        
        return "https://wa.me/" . $number . "?text=" . urlencode($message);
    }
}
