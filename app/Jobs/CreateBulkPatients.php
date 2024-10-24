<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VaahCms\Modules\Assignment\Models\Patient;
use Illuminate\Support\Facades\Log;

class CreateBulkPatients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $records;

    /**
     * Create a new job instance.
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info($this->records . ' Bulk Fake Patient Record Creation Process Initiated.');

        $i = 0;

        while($i < $this->records)
        {
            $inputs = Patient::fillItem(false);

            $item =  new Patient();
            $item->fill($inputs);
            $item->save();

            $i++;

        }

        Log::info($this->records . ' Bulk Fake Patient Record Creation Process Completed.');


    }
}
