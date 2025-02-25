<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2023. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Models;

class Currency extends StaticModel
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'exchange_rate' => 'float',
        'swap_currency_symbol' => 'boolean',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'precision' => 'integer',
    ];
}
