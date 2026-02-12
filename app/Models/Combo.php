<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'is_active',
    ];

    protected $appends = ['whatsapp_url'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_products')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function getWhatsappUrlAttribute(): string
    {
        $number = Setting::where('key', 'whatsapp_number')->value('value');
        $baseMessage = Setting::where('key', 'welcome_message')->value('value');
        
        if (!$number) return '#';

        $message = $baseMessage . " Combo: " . $this->name . " (Precio: $" . number_format($this->price, 2) . ")";
        
        return "https://wa.me/" . $number . "?text=" . urlencode($message);
    }
}
