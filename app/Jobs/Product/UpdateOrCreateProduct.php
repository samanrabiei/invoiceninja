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

namespace App\Jobs\Product;

use App\Libraries\MultiDB;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrCreateProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $products;

    public $invoice;

    public $company;

    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param $products
     * @param $invoice
     * @param $company
     */
    public function __construct($products, $invoice, $company)
    {
        $this->products = $products;

        $this->invoice = $invoice;

        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     *
     * @return void
     */
    public function handle()
    {
        MultiDB::setDB($this->company->db);

        if (strval($this->invoice->client->getSetting('currency_id')) != strval($this->company->settings->currency_id)) {
            return;
        }

        /*
         * If the invoice was generated from a Task or Expense then
         * we do NOT update the product details this short block we
         * check for the presence of a task_id and/or expense_id
         */
        $expense_count = count(array_column((array) $this->products, 'expense_id'));
        $task_count = count(array_column((array) $this->products, 'task_id'));

        if ($task_count >= 1 || $expense_count >= 1) {
            return;
        }

        //only update / create products - not tasks or gateway fees
        $updateable_products = collect($this->products)->filter(function ($item) {
            return $item->type_id == 1;
        });

        foreach ($updateable_products as $item) {
            if (empty($item->product_key)) {
                continue;
            }

            $product = Product::withTrashed()->firstOrNew(['product_key' => $item->product_key, 'company_id' => $this->invoice->company->id]);

                /* If a user is using placeholders in their descriptions, do not update the products */
                $string_hit = false;

                foreach ( [':MONTH',':YEAR',':QUARTER',':WEEK'] as $string ) 
                {
                
                    if(stripos($product->notes, $string) !== FALSE) {
                        $string_hit = true; 
                    }
                    
                }

                if($string_hit)
                    continue;

            $product->product_key = $item->product_key;
            $product->notes = isset($item->notes) ? $item->notes : '';
            $product->price = isset($item->cost) ? $item->cost : 0;

            if (! $product->id) {
                $product->quantity = isset($item->quantity) ? $item->quantity : 0;
            }

            $product->tax_name1 = isset($item->tax_name1) ? $item->tax_name1 : '';
            $product->tax_rate1 = isset($item->tax_rate1) ? $item->tax_rate1 : 0;
            $product->tax_name2 = isset($item->tax_name2) ? $item->tax_name2 : '';
            $product->tax_rate2 = isset($item->tax_rate2) ? $item->tax_rate2 : 0;
            $product->tax_name3 = isset($item->tax_name3) ? $item->tax_name3 : '';
            $product->tax_rate3 = isset($item->tax_rate3) ? $item->tax_rate3 : 0;

            if(isset($item->custom_value1) && strlen($item->custom_value1) >=1)
                $product->custom_value1 = $item->custom_value1;

            if(isset($item->custom_value2) && strlen($item->custom_value1) >=1)
                $product->custom_value2 = $item->custom_value2;
            
            if(isset($item->custom_value3) && strlen($item->custom_value1) >=1)
                $product->custom_value3 = $item->custom_value3;
            
            if(isset($item->custom_value4) && strlen($item->custom_value1) >=1)
                $product->custom_value4 = $item->custom_value4;
                       
            $product->user_id = $this->invoice->user_id;
            $product->company_id = $this->invoice->company_id;
            $product->project_id = $this->invoice->project_id;
            $product->vendor_id = $this->invoice->vendor_id;
            $product->save();
        }
    }

    public function failed($exception = null)
    {
        info('update create failed with = ');
        info(print_r($exception->getMessage(), 1));
    }
}
