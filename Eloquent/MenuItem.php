<?php declare(strict_types=1);

/*
 * This file is part of the Sepiphy package.
 *
 * (c) Quynh Xuan Nguyen <seriquynh@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sepiphy\Laravel\Menu\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class MenuItem extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'link',
        'icon',
        'position',
        'options',
        'menu_id',
        'parent_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'options' => 'object',
        'position' => 'string',
    ];

    public function getOptionsAttribute()
    {
        if (!isset($this->attributes['options'])) {
            return [];
        }

        if (is_array($this->attributes['options'])) {
            return $this->attributes['options'];
        } elseif (is_string($this->attributes['options'])) {
            $tmp = json_decode($this->attributes['options'], true);
            return json_decode($tmp, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->getOptionsAttribute();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Config::get('menu.eloquent.menu_item'), 'menu_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }
}
