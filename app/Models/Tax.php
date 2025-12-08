<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tax extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'default_tax_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'tax_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'tax_id');
    }
}
