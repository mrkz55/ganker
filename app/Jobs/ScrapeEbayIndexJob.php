<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Traits\EbayScraperTrait;

use App\Item;

class ScrapeEbayIndexJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, EbayScraperTrait;

    protected $result_url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($result_url)
    {

        Log::notice("Registered ScrapeEbayIndexJob for url {$result_url}");

        $this->result_url = $result_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $result_url = $this->result_url;

        Log::notice("Executing ScrapeEbayIndexJob for url {$result_url}");

        self::scrapeEbayIndex($result_url);

        Log::notice("ScrapeEbayIndexJob completed for url {$result_url}");

    }
}
