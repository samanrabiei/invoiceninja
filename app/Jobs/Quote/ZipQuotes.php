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

namespace App\Jobs\Quote;

use App\Jobs\Entity\CreateEntityPdf;
use App\Jobs\Mail\NinjaMailerJob;
use App\Jobs\Mail\NinjaMailerObject;
use App\Jobs\Util\UnlinkFile;
use App\Libraries\MultiDB;
use App\Mail\DownloadQuotes;
use App\Models\Company;
use App\Models\User;
use App\Utils\TempFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipQuotes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $quotes;

    private $company;

    private $user;

    public $settings;

    public $tries = 1;

    /**
     * @param $invoices
     * @param Company $company
     * @param $email
     * @deprecated confirm to be deleted
     * Create a new job instance.
     */
    public function __construct($quotes, Company $company, User $user)
    {
        $this->quotes = $quotes;

        $this->company = $company;

        $this->user = $user;

        $this->settings = $company->settings;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \ZipStream\Exception\FileNotFoundException
     * @throws \ZipStream\Exception\FileNotReadableException
     * @throws \ZipStream\Exception\OverflowException
     */
    public function handle()
    {
        MultiDB::setDb($this->company->db);

        // create new zip object
        $zipFile = new \PhpZip\ZipFile();
        $file_name = date('Y-m-d').'_'.str_replace(' ', '_', trans('texts.quotes')).'.zip';
        $invitation = $this->quotes->first()->invitations->first();
        $path = $this->quotes->first()->client->quote_filepath($invitation);

        $this->quotes->each(function ($quote) {
            (new CreateEntityPdf($quote->invitations()->first()))->handle();
        });

        try {
            foreach ($this->quotes as $quote) {
                $file = $quote->service()->getQuotePdf();
                $zip_file_name = basename($file);
                $zipFile->addFromString($zip_file_name, Storage::get($file));

                // $download_file = file_get_contents($quote->pdf_file_path($invitation, 'url', true));
                // $zipFile->addFromString(basename($quote->pdf_file_path($invitation)), $download_file);
            }

            Storage::put($path.$file_name, $zipFile->outputAsString());

            $nmo = new NinjaMailerObject;
            $nmo->mailable = new DownloadQuotes(Storage::url($path.$file_name), $this->company);
            $nmo->to_user = $this->user;
            $nmo->settings = $this->settings;
            $nmo->company = $this->company;

            NinjaMailerJob::dispatch($nmo);

            UnlinkFile::dispatch(config('filesystems.default'), $path.$file_name)->delay(now()->addHours(1));
        } catch (\PhpZip\Exception\ZipException $e) {
            // handle exception
        } finally {
            $zipFile->close();
        }
    }
}
