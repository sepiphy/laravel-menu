<?php declare(strict_types=1);

/*
 * This file is part of the Sepiphy package.
 *
 * (c) Quynh Xuan Nguyen <seriquynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sepiphy\Laravel\Menu\DisplayerInterface;

if (! function_exists('menu')) {
    /**
     * @param  string  $code
     * @param  string|null  $type
     * @param  array|null  $options
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     *
     * @throws \Exception
     */
    function menu(string $code, string $type = null, array $options = null)
    {
        return app(DisplayerInterface::class)->render($code, $type, $options);
    }
}
