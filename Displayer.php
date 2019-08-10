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
use Sepiphy\Laravel\Menu\Eloquent\MenuItem;

class Displayer
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
        $viewName = $this->findViewName($code, $type);

        $menu = $this->findMenu($code);

        return new HtmlString(
            View::make($viewName)->with('menu', $menu)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function nestable($menu)
    {
        return $this->getNestableFor($menu);
    }

    /**
     * {@inheritdoc}
     */
    public function visible(Closure $callback)
    {
        $this->visibleCallback = $callback;
    }

    public function active(Closure $callback)
    {
        $this->activeCallback = $callback;
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function findViewName(string $code, string $type = null, array $options = null)
    {
        $type = $type ?: $code;

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
        $class = Config::get('menu.eloquent.menu');

        $menu = App::make($class)->where('code', $code)->first();

        if ($menu) {
            $menu->items = $this->getNestableFor($menu);
        } else {
            $menu = App::make($class);
            $menu->items = collect([]);
        }

        return $menu;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getNestableFor($menu)
    {
        $items = App::make(Config::get('menu.eloquent.menu_item'))
            ->where('menu_id', $menu->getKey())
            ->orderBy('position')
            ->orderBy('parent_id')
            ->get()
        ;

        foreach ($items as $i => $item) {
            if ($this->hide($item)) {
                $items->pull($i);
                continue;
            }

            $item->children = $this->getChildrenForParent($items, $item);
        }

        $this->prepareItems($items);

        return $items;
    }

    /**
     * @param  \Illuminate\Support\Collection  $items
     * @param  \Sepiphy\Laravel\Menu\Eloquent\MenuItem  $parent
     * @return \Illuminate\Support\Collection
     */
    protected function getChildrenForParent(Collection $items, MenuItem $parent)
    {
        $children = collect([]);

        foreach ($items as $i => $item) {
            if ($item->parent_id === $parent->getKey()) {
                $children->push($items->pull($i));
            }
        }

        return $children;
    }

    /**
     * @param  \Illuminate\Support\Collection  $items
     * @param  \Sepiphy\Laravel\Menu\Eloquent\MenuItem|null  $parent
     * @return void
     */
    protected function prepareItems(Collection $items, MenuItem $parent = null)
    {
        foreach ($items as $i => $item) {
            if ($this->hide($item)) {
                $items->pull($i);
                continue;
            }

            if (URL::to($item->link) === Request::url()) {
                if (! is_null($parent)) {
                    $parent->active = true;
                }

                $item->active = true;
            } else {
                $item->active = false;
            }

            if ($item->children->isNotEmpty()) {
                $this->prepareItems($item->children, $item);
            }
        }
    }

    /**
     * @param  \Closure  $callback
     * @return bool
     */
    protected function hide($item)
    {
        return $this->visibleCallback && ! call_user_func($this->visibleCallback, $item);
    }
}
