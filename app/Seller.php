<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;

class Seller extends User
{

    // Es para iniciar el modelo
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SellerScope);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
