<?php

namespace Qihucms\UCenter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Qihucms\UCenter\Models\UcenterSite;
use Qihucms\UCenter\Support;

class UpdateAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $sites = UcenterSite::where('status', 1)->whereNotNull('account_api')->get();
        foreach ($sites as $site) {
            Support::requestApi($site->account_api, $this->data);
        }
    }
}
