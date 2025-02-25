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

namespace App\Filters;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

/**
 * DocumentFilters.
 */
class DocumentFilters extends QueryFilters
{
    /**
     * Filter based on search text.
     *
     * @param string query filter
     * @return Builder
     * @deprecated
     */
    public function filter(string $filter = ''): Builder
    {
        if (strlen($filter) == 0) {
            return $this->builder;
        }

        return $this->builder;
    }

    /**
     * Overriding method as client_id does
     * not exist on this model, just pass
     * back the builder
     * @param  string $client_id The client hashed id.
     * 
     * @return Builder           
     */
    public function client_id(string $client_id = ''): Builder
    {
        return $this->builder;
    }

    /**
     * Sorts the list based on $sort.
     *
     * @param string sort formatted as column|asc
     * @return Builder
     */
    public function sort(string $sort = '') : Builder
    {
        $sort_col = explode('|', $sort);

        if(is_array($sort_col))
            return $this->builder->orderBy($sort_col[0], $sort_col[1]);

        return $this->builder;
    }


    public function company_documents($value = 'false')
    {
        if($value == 'true')
            return $this->builder->where('documentable_type', Company::class);
    
        return $this->builder;
    }

    /**
     * Filters the query by the users company ID.
     *
     * @return Illuminate\Database\Query\Builder
     */
    public function entityFilter()
    {
        return $this->builder->company();
    }
}
