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

use Closure;
use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Sepiphy\Laravel\Menu\Models\Menu;
use Sepiphy\Laravel\Menu\Models\MenuItem;

class Displayer implements DisplayerInterface
{
    /**
     * @var array
     */
    protected $views = [];

    /**
     * @var \Closure
     */
    protected $visibleCallback;

    /**
     * @var \Closure
     */
    protected $activeCallback;

    /**
     * {@inheritdoc}
     */
    public function register(string $type, string $viewName)
    {
        $this->views[$type] = $viewName;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $code, string $type = null, array $options = null)
    {
        $viewName = $this->findViewName($type);

        $menu = $this->findMenu($code);

        $view = View::make($viewName)->with('menu', $menu)->with('options', $options);

        return new HtmlString(
            method_exists($view, 'render') ? $view->render() : (string) $view
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visibleUsing(Closure $callback)
    {
        $this->visibleCallback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function activeUsing(Closure $callback)
    {
        $this->activeCallback = $callback;
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function findViewName(string $type = null)
    {
        $type = $type ?: 'default';

        if (! array_key_exists($type, $this->views)) {
            throw new RuntimeException(sprintf('The menu type "%s" is not supported.', $type));
        }

        return $this->views[$type];
    }

    /**
     * @return mixed
     */
    protected function findMenu(string $code)
    {
        $class = Config::get('menu.model.menu');

        $menu = App::make($class)->where('code', $code)->first();

        if ($menu) {
            $menu->items = $this->getChildrenFor($menu);
        } else {
            $menu = App::make($class);
            $menu->items = collect([]);
        }

        return $menu;
    }

    /**
     * @param  Menu  $menu
     * @return \Illuminate\Support\Collection
     */
    protected function getChildrenFor(Menu $menu)
    {
        $menuItems = App::make(Config::get('menu.model.menu_item'))
            ->where('menu_id', $menu->getKey())
            ->orderBy('position')
            ->orderBy('parent_id')
            ->get()
        ;

        foreach ($menuItems as $i => $menuItem) {
            if (!$this->isItemVisible($menuItem)) {
                $menuItems->pull($i);
                continue;
            }

            $menuItem->children = $this->getChildrenForParent($menuItems, $menuItem);
        }

        $this->prepareItems($menuItems);

        return $menuItems;
    }

    /**
     * @param  \Illuminate\Support\Collection  $menuItems
     * @param  \Sepiphy\Laravel\Menu\Models\MenuItem  $parent
     * @return \Illuminate\Support\Collection
     */
    protected function getChildrenForParent(Collection $menuItems, MenuItem $parent)
    {
        $children = collect([]);

        foreach ($menuItems as $i => $menuItem) {
            if ($menuItem->parent_id == $parent->getKey()) {
                $children->push($menuItems->pull($i));
            }
        }

        return $children;
    }

    /**
     * @param  \Illuminate\Support\Collection  $menuItems
     * @param  \Sepiphy\Laravel\Menu\Models\MenuItem|null  $parent
     * @return void
     */
    protected function prepareItems(Collection $menuItems, MenuItem $parent = null)
    {
        foreach ($menuItems as $i => $menuItem) {
            if (!$this->isItemVisible($menuItem)) {
                $menuItems->pull($i);
                continue;
            }

            if ($this->isItemActive($menuItem)) {
                if (! is_null($parent)) {
                    $parent->open = true;
                    $parent->active = true;
                }

                $menuItem->active = true;
            } else {
                $menuItem->active = false;
            }

            if ($menuItem->children->isNotEmpty()) {
                $this->prepareItems($menuItem->children, $menuItem);
            }
        }
    }

    /**
     * @param  MenuItem  $menuItem
     * @return bool
     */
    protected function isItemVisible(MenuItem $menuItem)
    {
        return !is_null($this->visibleCallback) && call_user_func($this->visibleCallback, $menuItem);
    }

    /**
     * @param  MenuItem  $menuItem
     * @return bool
     */
    protected function isItemActive(MenuItem $menuItem)
    {
        return !is_null($this->activeCallback)  && call_user_func($this->activeCallback, $menuItem);
    }
}
