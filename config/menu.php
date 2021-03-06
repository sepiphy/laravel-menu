<?php declare(strict_types=1);

/*
 * This file is part of the Sepiphy package.
 *
 * (c) Quynh Xuan Nguyen <seriquynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'model' => [
        'menu' => Sepiphy\Laravel\Menu\Models\Menu::class,
        'menu_item' => Sepiphy\Laravel\Menu\Models\MenuItem::class,
    ],

    'table' => [
        'menus' => 'menus',
        'menu_items' => 'menu_items',
    ],

    'views' => [
        //
    ],

];
