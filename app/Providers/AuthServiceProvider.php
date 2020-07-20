<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Policies\BuyerPolicy;
use Carbon\Carbon;
use App\Buyer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        // 'App\Buyer' => 'App\Policies\BuyerPolicy',
        Buyer::class => BuyerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn( Carbon::now()->addMinutes(30) ); // tiempo que dura el token, es de 30 minutos
        Passport::refreshTokensExpireIn( Carbon::now()->addDays(30) ); // tiempo limite para refrescar el token, es de 30 días
        Passport::enableImplicitGrant(); // para obtener tokens de esta manera

        Passport::tokensCan([
            'purchase-product' => 'Crear transacciones para comprar productos determinados',
            'manage-products'  => 'Crear, ver, actualizar y eliminar productos',
            'manage-account'   => 'Obtener la información de la cuenta, nombre, email, estado 
                                   (sin contraseña), modificar datos como email, nombre y contraseña. 
                                   No puede eliminar la cuenta',
            'read-general'     => 'Obtener información general, categorías donde se compra y se vende,
                                   prodcutos vendidos o comprados, transacciones, compras y ventas',

        ]);
    }
}
