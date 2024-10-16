<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use VaahCms\Modules\Assignment\Models\Doctor;
use Carbon\Carbon;

class ExportDoctorsData implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Specialization',
            'Email',
            'Phone',
            'consultation_fees',
            'working_hours_start',
            'working_hours_end',
            'Created_at',
            'Updated_at'
        ];
    }

    public function collection()
    {
        return Doctor::all()->map(function ($doctor) {
            return [
                'ID' => $doctor->id,
                'Name' => $doctor->name,
                'Specialization' => $doctor->specialization,
                'Email' => $doctor->email,
                'Phone' => $doctor->phone,
                'consultation_fees' => $doctor->consultation_fees,
                'working_hours_start' => Carbon::parse($doctor->working_hours_start)->format('h:i:s A'),
                'working_hours_end' => Carbon::parse($doctor->working_hours_end)->format('h:i:s A'),
                'created_at' => Carbon::parse($doctor->created_at)->format('Y-m-d h:i:s A'),
                'updated_at' => Carbon::parse($doctor->updated_at)->format('Y-m-d h:i:s A')
            ];
        });
    }

}
