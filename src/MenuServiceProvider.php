<?php declare(strict_types=1);

/*
 * This file is part of the Sepiphy package.
 *
 * (c) Quynh Xuan Nguyen <seriquynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sepiphy\Laravel\Menu;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/menu.php', 'menu');

        $this->app->singleton(DisplayerInterface::class, function ($app) {
            $this->loadViewsFrom(__DIR__.'/../views', 'menu');

            $displayer = new Displayer();

            $displayer->register('default', 'menu::default');
            $displayer->register('lteside', 'menu::lteside');

            foreach ($app['config']['menu.views'] as $type => $viewName) {
                $displayer->register($type, $viewName);
            }

            return $displayer;
        });

        $this->app->alias(DisplayerInterface::class, Displayer::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
