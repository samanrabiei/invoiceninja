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

class Country extends StaticModel
{
    public $timestamps = false;

    protected $casts = [
        'eea' => 'boolean',
        'swap_postal_code' => 'boolean',
        'swap_currency_symbol' => 'boolean',
        'thousand_separator' => 'string',
        'decimal_separator' => 'string',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Localizes the country name for the clients language.
     *
     * @return string The translated country name
     */
    public function getName() :string
    {
        return trans('texts.country_'.$this->name);
    }
      	public function getID() :string
    {
      return $this->id;
    }
}
