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
        'is_offer',
        'is_active',
    ];

    protected $appends = ['whatsapp_url'];

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
        $number = Setting::where('key', 'whatsapp_number')->value('value');
        $baseMessage = Setting::where('key', 'welcome_message')->value('value');
        
        if (!$number) return '#';

        $message = $baseMessage . " " . $this->name . " (Precio: $" . number_format($this->discount_price ?? $this->price, 2) . ")";
        
        return "https://wa.me/" . $number . "?text=" . urlencode($message);
    }
}
