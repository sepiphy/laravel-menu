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

        $this->app->singleton(Displayer::class);
    }

    /**
     * @return void
     */
    public function boot()
    {
        foreach ($this->app['config']->get('menu.views') as $type => $viewName) {
            $this->app[Displayer::class]->register($type, $viewName);
        }
    }
}
