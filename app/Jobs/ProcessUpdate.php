<?php

namespace App\Jobs;

use App\Http\Controllers\CRest;
use App\Models\History;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $history = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($history)
    {
        $this->history = $history;
    }

    public function handle()
    {
        CRest::callBatch($this->history);
    }
}
