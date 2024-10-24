<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VaahCms\Modules\Assignment\Models\Doctor;
use Illuminate\Support\Facades\Log;

class CreateBulkDoctors implements ShouldQueue
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

        Log::info($this->records . " Bulk Fake Doctor Creation Process Initiated");

        $i = 0;

        while($i < $this->records)
        {
            $inputs = Doctor::fillItem(false);

            $item =  new Doctor();
            $item->fill($inputs);
            $item->save();

            $i++;

        }

        Log::info($this->records . " Bulk Fake Doctor Creation Process Completed");

    }
}
