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

namespace App\Http\Requests\Client;

use App\Http\Requests\Request;

class UploadClientRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return auth()->user()->can('edit', $this->client);
    }

    public function rules()
    {
        $rules = [];

        if ($this->input('documents')) {
            $rules['documents'] = 'file|mimes:csv,png,ai,jpeg,tiff,pdf,gif,psd,txt,doc,xls,ppt,xlsx,docx,pptx|max:2000000';
        }

        return $rules;
    }
}
