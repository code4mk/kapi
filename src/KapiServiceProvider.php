<?php

namespace Kapi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Kapi\ApiApp\ApiApp;
use Kapi\Oauth\Koauth;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @since     2019
 * @copyright 0dev.co (https://0dev.co)
 */

class KapiServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot(\Illuminate\Routing\Router $router)
   {
     // publish database
      $this->publishes([
       __DIR__.'/../migrations/' => database_path('migrations'),
      ], 'migrations');
      
      // publish config
       $this->publishes([
        __DIR__ . '/../config/kapi.php' => config_path('kapi.php'),
       ], 'config');
      //load alias
      AliasLoader::getInstance()->alias('Kapi', 'Kapi\Facades\Api');
      AliasLoader::getInstance()->alias('Koauth', 'Kapi\Facades\Oauth');
      // load middleware group
      $router->middlewareGroup('kapi',[
        \Kapi\Middleware\KapiWare::class,
        \Kapi\Middleware\EtagWare::class,
        'throttle:60,1',
        'bindings'
      ]);

      $router->middlewareGroup('kapix',[
        \Kapi\Middleware\KapiSpa::class,
        \Kapi\Middleware\EtagWare::class,
        'throttle:60,1',
        'bindings'
      ]);
   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
     $this->app->bind('kapi', function () {
      return new ApiApp;
     });
     $this->app->bind('koauth', function () {
      return new Koauth;
     });
   }
}
