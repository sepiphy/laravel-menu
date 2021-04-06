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
use Illuminate\Support\HtmlString;

interface DisplayerInterface
{
    /**
     * Register a pair type and view name of a.
     *
     * @param  string  $type
     * @param  string  $viewName
     * @return void
     */
    public function register(string $type, string $viewName);

    /**
     * Render a menu as html string instance.
     *
     * @param  string  $code
     * @param  string  $type
     * @param  array|null  $options
     * @return HtmlString
     */
    public function render(string $code, string $type = null, array $options = null);

    /**
     * Determine whether a menu item is visible.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function visibleUsing(Closure $callback);

    /**
     * Determine whether a menu item is active.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function activeUsing(Closure $callback);
}
