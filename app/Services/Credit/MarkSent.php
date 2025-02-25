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

namespace App\Services\Credit;

use App\Events\Credit\CreditWasMarkedSent;
use App\Models\Credit;
use App\Utils\Ninja;

class MarkSent
{
    private $client;

    private $credit;

    public function __construct($client, $credit)
    {
        $this->client = $client;
        $this->credit = $credit;
    }

    public function run()
    {

        /* Return immediately if status is not draft */
        if ($this->credit->status_id != Credit::STATUS_DRAFT) {
            return $this->credit;
        }

        $this->credit->markInvitationsSent();

        $this->credit
             ->service()
             ->setStatus(Credit::STATUS_SENT)
             ->applyNumber()
             ->adjustBalance($this->credit->amount)
             ->touchPdf()
             ->save();

        $this->client
             ->service()
             ->adjustCreditBalance($this->credit->amount)
             ->save();
             
        event(new CreditWasMarkedSent($this->credit, $this->credit->company, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));

        return $this->credit;
    }
}
