<?php

namespace App\Export;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ExportSampleCSV implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Patient Email',
            "Doctor Email",
            "Date",
            'Time',
            'Status'
        ];
    }

    public function collection()
    {
        return new Collection([
            [
                'Patient_Email' => 'patient@sample.com',
                'Doctor_Email' => 'doctor@sample.com',
                'Date' => Carbon::today()->toDateString(),
                'Time' => Carbon::now()->format('h:i A'),
                'Status' => 'Booked/Cancelled'
            ]
        ]);
    }

}
