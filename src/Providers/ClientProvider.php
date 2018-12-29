<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 2018-12-28
 * Time: 23:21
 */

namespace Ebookr\Client\Providers;

use Illuminate\Support\ServiceProvider;

class ClientProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
            __DIR__ . '/../../config/e-bookr.php' => config_path('e-bookr.php')
            ]
        );
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../views', 'e-bookr');
    }

    public function register()
    {

    }
}