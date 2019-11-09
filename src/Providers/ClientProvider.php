<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 2018-12-28
 * Time: 23:21
 */

namespace Ebookr\Client\Providers;

use Ebookr\Client\Console\FindTranslationCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ClientProvider extends ServiceProvider
{
    public function boot()
    {
        Collection::macro(
            'setVisible',
            function ($attributes) {
                return $this->map(
                    function (Model $item) use ($attributes) {
                        return $item->setVisible($attributes);
                    }
                );
            }
        );

        Collection::macro(
            'onlyAs',
            function ($attributes) {
                return $this->map(
                    function (Model $item) use ($attributes) {
                        $elem = clone $item;
                        foreach ($attributes as $key => $alias) {
                            $elem->setAttribute($alias, $elem->getAttributeValue($key));
                        }

                        return $elem->setVisible(array_values($attributes));
                    }
                );
            }
        );

        $this->publishes([__DIR__ . '/../../config/e-bookr.php' => config_path('e-bookr.php'),]);
        $this->publishes([__DIR__ . '/../../dist' => public_path('vendor/e-bookr')], 'public');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../views', 'e-bookr');

        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    FindTranslationCommand::class
                ]
            );
        }
    }

    public function register()
    {

    }
}