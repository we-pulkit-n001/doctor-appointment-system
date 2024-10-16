<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use VaahCms\Modules\Assignment\Models\Patient;
use Carbon\Carbon;

class ExportPatientsData implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Created_at',
            'Updated_at'
        ];
    }

    public function collection()
    {
        return Patient::all()->map(function ($patient) {
            return [
                'ID' => $patient->id,
                'Name' => $patient->name,
                'Email' => $patient->email,
                'Phone' => $patient->phone,
                'created_at' => Carbon::parse($patient->created_at)->format('Y-m-d h:i:s A'),
                'updated_at' => Carbon::parse($patient->updated_at)->format('Y-m-d h:i:s A')
            ];
        });
    }
    
}
