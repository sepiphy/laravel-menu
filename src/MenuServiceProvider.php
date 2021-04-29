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

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Sepiphy\Laravel\Menu\Models\MenuItem;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/menu.php', 'menu');

        $this->app->singleton(DisplayerInterface::class, Displayer::class);
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'menu');

        $displayer = $this->app[DisplayerInterface::class];

        $displayer->register('default', 'menu::default');
        $displayer->register('lteside', 'menu::lteside');

        foreach ($this->app['config']['menu.views'] as $type => $viewName) {
            $displayer->register($type, $viewName);
        }

        $displayer->visibleUsing(function (MenuItem $menuItem) {
            return true;
        });

        $displayer->activeUsing(function (MenuItem $menuItem) {
            return URL::to($menuItem->link) === Request::url();
        });
    }
}
