<?php namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        /**
         * Al momento de crear un usuario se ejecuta esta funcion
         */
        User::created( function($user) {
            Mail::to($user->email)->send(new UserCreated($user));
        });

        /**
         * Al momento de actualizar el correo del usuario
         */
        User::updated( function($user) {
            // verifica si se modifico el email
            if ( $user->isDirty('email') ) {
                Mail::to($user->email)->send(new UserMailChanged($user));
            }
        });

        /**
         * Al momento de actualizar un producto se ejecuta esta funcion
         */
        Product::updated( function($product) {
            if( $product->quantity == 0 && $product->estaDisponible() ) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;
                $product->save();
            }
        });

    }
}
