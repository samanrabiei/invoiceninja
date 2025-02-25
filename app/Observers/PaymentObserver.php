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

namespace App\Observers;

use App\Jobs\Util\WebhookHandler;
use App\Models\Payment;
use App\Models\Webhook;

class PaymentObserver
{
    /**
     * Handle the payment "created" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function created(Payment $payment)
    {
        $subscriptions = Webhook::where('company_id', $payment->company->id)
                            ->where('event_id', Webhook::EVENT_CREATE_PAYMENT)
                            ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_CREATE_PAYMENT, $payment, $payment->company, 'invoices,client')->delay(now()->addSeconds(20));
        }
    }

    /**
     * Handle the payment "updated" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function updated(Payment $payment)
    {
        $subscriptions = Webhook::where('company_id', $payment->company->id)
                            ->where('event_id', Webhook::EVENT_UPDATE_PAYMENT)
                            ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_UPDATE_PAYMENT, $payment, $payment->company, 'invoices,client')->delay(now()->addSeconds(20));
        }
    }

    /**
     * Handle the payment "deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        $subscriptions = Webhook::where('company_id', $payment->company->id)
                        ->where('event_id', Webhook::EVENT_DELETE_PAYMENT)
                        ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_DELETE_PAYMENT, $payment, $payment->company, 'invoices,client')->delay(now()->addSeconds(20));
        }
    }

    /**
     * Handle the payment "restored" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function restored(Payment $payment)
    {
        //
    }

    /**
     * Handle the payment "force deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function forceDeleted(Payment $payment)
    {
        //
    }
}
