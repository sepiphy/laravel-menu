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
        $this->mergeConfigFrom(__DIR__.'/config/menu.php', 'menu');

        $this->app->singleton(DisplayerInterface::class, function () {
            return new Displayer();
        });

        $this->app->alias(DisplayerInterface::class, Displayer::class);
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'menu');

        $this->app[DisplayerInterface::class]->register('default', 'menu::default');

        foreach ($this->app['config']->get('menu.views') as $type => $viewName) {
            $this->app[DisplayerInterface::class]->register($type, $viewName);
        }
    }
}
