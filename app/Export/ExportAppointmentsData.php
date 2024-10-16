<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use VaahCms\Modules\Assignment\Models\Appointment;
use VaahCms\Modules\Assignment\Models\Doctor;
use Carbon\Carbon;
use VaahCms\Modules\Assignment\Models\Patient;

class ExportAppointmentsData implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID',
            "Patient's Name",
            "Doctor's Name",
            'Date',
            'Time',
            'Status',
            'Created_at',
            'Updated_at'
        ];
    }

    public function collection()
    {
        return Appointment::all()->map(function ($appointment) {

            $doctor_name = Doctor::find($appointment['doctor_id']);
            $patient_name = Patient::find($appointment['doctor_id']);

            return [
                'ID' => $appointment->id,
                'Patient_Name' => $doctor_name['name'] ?? 'N/A',
                'Doctor_Name' => $patient_name['name'] ?? 'N/A',
                'Date' => $appointment->date,
                'Time' => Carbon::parse($appointment->time)->format('h:i:s A'),
                'Status' => $appointment->status,
                'created_at' => Carbon::parse($appointment->created_at)->format('Y-m-d h:i:s A'),
                'updated_at' => Carbon::parse($appointment->updated_at)->format('Y-m-d h:i:s A')
            ];
        });
    }

}
