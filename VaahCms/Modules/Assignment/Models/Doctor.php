<?php namespace VaahCms\Modules\Assignment\Models;

use App\Export\ExportDoctorsData;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Faker\Factory;
use Maatwebsite\Excel\Facades\Excel;
use WebReinvent\VaahCms\Libraries\VaahMail;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Libraries\VaahSeeder;
use Carbon\Carbon;

class Doctor extends VaahModel
{

    use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'vh_doctors';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'name',
        'specialization',
        'email',
        'phone',
        'working_hours_start',
        'working_hours_end',
        'consultation_fees',
        'slug',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    //-------------------------------------------------
    protected $fill_except = [

    ];

    //-------------------------------------------------
    protected $appends = [
    ];

    //-------------------------------------------------
    protected function serializeDate(DateTimeInterface $date)
    {
        $date_time_format = config('settings.global.datetime_format');
        return $date->format($date_time_format);
    }

    //-------------------------------------------------
    public static function getUnFillableColumns()
    {
        return [
            'uuid',
            'created_by',
            'updated_by',
            'deleted_by',
        ];
    }
    //-------------------------------------------------
    public static function getFillableColumns()
    {
        $model = new self();
        $except = $model->fill_except;
        $fillable_columns = $model->getFillable();
        $fillable_columns = array_diff(
            $fillable_columns, $except
        );
        return $fillable_columns;
    }
    //-------------------------------------------------
    public static function getEmptyItem()
    {
        $model = new self();
        $fillable = $model->getFillable();
        $empty_item = [];
        foreach ($fillable as $column)
        {
            $empty_item[$column] = null;
        }

        $empty_item['is_active'] = 1;

        return $empty_item;
    }

    //-------------------------------------------------

    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }

    //-------------------------------------------------
    public function scopeExclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), $columns));
    }

    //-------------------------------------------------
    public function scopeBetweenDates($query, $from, $to)
    {

        if ($from) {
            $from = \Carbon::parse($from)
                ->startOfDay()
                ->toDateTimeString();
        }

        if ($to) {
            $to = \Carbon::parse($to)
                ->endOfDay()
                ->toDateTimeString();
        }

        $query->whereBetween('updated_at', [$from, $to]);
    }

    //-------------------------------------------------
    public static function createItem($request)
    {

        $inputs = $request->all();

        $validation = self::validation($inputs);
        if (!$validation['success']) {
            return $validation;
        }

        $inputs['is_active'] = 1;

        $inputs['working_hours_start'] = Carbon::parse($inputs['working_hours_start'])->setTimezone('Asia/Kolkata')->format('H:i:s');
        $inputs['working_hours_end'] = Carbon::parse($inputs['working_hours_end'])->setTimezone('Asia/Kolkata')->format('H:i:s');

        // check if name exist
        $item = self::where('name', $inputs['name'])->withTrashed()->first();

        if ($item) {
            $error_message = "This name is already exist".($item->deleted_at?' in trash.':'.');
            $response['success'] = false;
            $response['messages'][] = $error_message;
            return $response;
        }

        // check if slug exist
        $item = self::where('slug', $inputs['slug'])->withTrashed()->first();

        if ($item) {
            $error_message = "This slug is already exist".($item->deleted_at?' in trash.':'.');
            $response['success'] = false;
            $response['messages'][] = $error_message;
            return $response;
        }

        $item = new self();
        $item->fill($inputs);
        $item->save();

        self::doctorCreatedMail($inputs);

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }

    //-------------------------------------------------
    public function scopeGetSorted($query, $filter)
    {

        if(!isset($filter['sort']))
        {
            return $query->orderBy('id', 'desc');
        }

        $sort = $filter['sort'];


        $direction = Str::contains($sort, ':');

        if(!$direction)
        {
            return $query->orderBy($sort, 'asc');
        }

        $sort = explode(':', $sort);

        return $query->orderBy($sort[0], $sort[1]);
    }
    //-------------------------------------------------
    public function scopeIsActiveFilter($query, $filter)
    {

        if(!isset($filter['is_active'])
            || is_null($filter['is_active'])
            || $filter['is_active'] === 'null'
        )
        {
            return $query;
        }
        $is_active = $filter['is_active'];

        if($is_active === 'true' || $is_active === true)
        {
            return $query->where('is_active', 1);
        } else{
            return $query->where(function ($q){
                $q->whereNull('is_active')
                    ->orWhere('is_active', 0);
            });
        }

    }
    //-------------------------------------------------
    public function scopeTrashedFilter($query, $filter)
    {

        if(!isset($filter['trashed']))
        {
            return $query;
        }
        $trashed = $filter['trashed'];

        if($trashed === 'include')
        {
            return $query->withTrashed();
        } else if($trashed === 'only'){
            return $query->onlyTrashed();
        }

    }
    //-------------------------------------------------
    public function scopeSearchFilter($query, $filter)
    {

        if(!isset($filter['q']))
        {
            return $query;
        }
        $search_array = explode(' ',$filter['q']);
        foreach ($search_array as $search_item){
            $query->where(function ($q1) use ($search_item) {
                $q1->where('name', 'LIKE', '%' . $search_item . '%')
                    ->orWhere('email', 'LIKE', '%' . $search_item . '%')
//                    ->orWhere('id', 'LIKE', $search_item . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search_item . '%');
            });
        }

    }
    //-------------------------------------------------
    public static function getList($request)
    {

        $list = self::getSorted($request->filter);
        $list->isActiveFilter($request->filter);
        $list->trashedFilter($request->filter);
        $list->searchFilter($request->filter);

        $rows = config('vaahcms.per_page');

        if($request->has('rows'))
        {
            $rows = $request->rows;
        }

        $list = $list->paginate($rows);

        $response['success'] = true;
        $response['data'] = $list;

        return $response;

    }

    //-------------------------------------------------
    public static function updateList($request)
    {

        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
        );


        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        if(isset($inputs['items']))
        {
            $items_id = collect($inputs['items'])
                ->pluck('id')
                ->toArray();
        }

        $items = self::whereIn('id', $items_id);

        switch ($inputs['type']) {
            case 'deactivate':
                $items->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'activate':
                $items->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'trash':
                self::whereIn('id', $items_id)
                    ->get()->each->delete();
                break;
            case 'restore':
                self::whereIn('id', $items_id)->onlyTrashed()
                    ->get()->each->restore();
                break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }

    //-------------------------------------------------
    public static function deleteList($request): array
    {

        $inputs = $request->all();

        $rules = array(
            'type' => 'required',
            'items' => 'required',
        );

        $messages = array(
            'type.required' => trans("vaahcms-general.action_type_is_required"),
            'items.required' => trans("vaahcms-general.select_items"),
        );

        $validator = \Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {

            $errors = errorsToArray($validator->errors());
            $response['success'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        $items_id = collect($inputs['items'])->pluck('id')->toArray();
        self::whereIn('id', $items_id)->forceDelete();

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
     public static function listAction($request, $type): array
    {

        $list = self::query();

        if($request->has('filter')){
            $list->getSorted($request->filter);
            $list->isActiveFilter($request->filter);
            $list->trashedFilter($request->filter);
            $list->searchFilter($request->filter);
        }

        switch ($type) {
            case 'activate-all':
                $list->withTrashed()->where(function ($q){
                    $q->where('is_active', 0)->orWhereNull('is_active');
                })->update(['is_active' => 1]);
                break;
            case 'deactivate-all':
                $list->withTrashed()->where(['is_active' => 1])
                    ->update(['is_active' => null]);
                break;
            case 'trash-all':
                $list->get()->each->delete();
                break;
            case 'restore-all':
                $list->onlyTrashed()->get()
                    ->each->restore();
                break;
            case 'delete-all':
                $list->forceDelete();
                break;
            case 'create-100-records':
            case 'create-1000-records':
            case 'create-5000-records':
            case 'create-10000-records':

            if(!config('assignment.is_dev')){
                $response['success'] = false;
                $response['errors'][] = 'User is not in the development environment.';

                return $response;
            }

            preg_match('/-(.*?)-/', $type, $matches);

            if(count($matches) !== 2){
                break;
            }

            self::seedSampleItems($matches[1]);
            break;
        }

        $response['success'] = true;
        $response['data'] = true;
        $response['messages'][] = trans("vaahcms-general.action_successful");

        return $response;
    }
    //-------------------------------------------------
    public static function getItem($id)
    {

        $item = self::where('id', $id)
            ->with(['createdByUser', 'updatedByUser', 'deletedByUser'])
            ->withTrashed()
            ->first();

        if(!$item)
        {
            $response['success'] = false;
            $response['errors'][] = 'Record not found with ID: '.$id;
            return $response;
        }
        $response['success'] = true;
        $response['data'] = $item;

        return $response;

    }
    //-------------------------------------------------
    public static function updateItem($request, $id)
    {
        $inputs = $request->all();

        $doctor = self::find($inputs['id']);

        if($doctor['email'] != $inputs['email'] || $doctor['phone'] != $inputs['phone']){
            $validation = self::validation($inputs);
            if (!$validation['success']) {
                return $validation;
            }
        }

        // check if name exist
        $item = self::where('id', '!=', $id)
            ->withTrashed()
            ->where('name', $inputs['name'])->first();

         if ($item) {
             $error_message = "This name is already exist".($item->deleted_at?' in trash.':'.');
             $response['success'] = false;
             $response['errors'][] = $error_message;
             return $response;
         }

         // check if slug exist
         $item = self::where('id', '!=', $id)
             ->withTrashed()
             ->where('slug', $inputs['slug'])->first();

         if ($item) {
             $error_message = "This slug is already exist".($item->deleted_at?' in trash.':'.');
             $response['success'] = false;
             $response['errors'][] = $error_message;
             return $response;
         }

        $item = self::where('id', $id)->withTrashed()->first();
        $item->fill($inputs);
        $item->save();

        if($doctor['working_hours_start'] != $inputs['working_hours_start'] || $doctor['working_hours_end'] != $inputs['working_hours_end']){
            self::availabilityUpdatedMail($inputs);
        }

        $response = self::getItem($item->id);
        $response['messages'][] = trans("vaahcms-general.saved_successfully");
        return $response;

    }
    //-------------------------------------------------
    public static function deleteItem($request, $id): array
    {
        $item = self::where('id', $id)->withTrashed()->first();
        if (!$item) {
            $response['success'] = false;
            $response['errors'][] = trans("vaahcms-general.record_does_not_exist");
            return $response;
        }
        $item->forceDelete();

        $response['success'] = true;
        $response['data'] = [];
        $response['messages'][] = trans("vaahcms-general.record_has_been_deleted");

        return $response;
    }
    //-------------------------------------------------
    public static function itemAction($request, $id, $type): array
    {

        switch($type)
        {
            case 'activate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => 1]);
                break;
            case 'deactivate':
                self::where('id', $id)
                    ->withTrashed()
                    ->update(['is_active' => null]);
                break;
            case 'trash':
                self::doctorDeletedMail($id);
                self::find($id)
                    ->delete();
                break;
            case 'restore':
                self::where('id', $id)
                    ->onlyTrashed()
                    ->first()->restore();
                break;
        }

        return self::getItem($id);
    }
    //-------------------------------------------------

    public static function validation($inputs)
    {

        $rules = array(
            'name' => 'required|max:150',
            'specialization' => 'required|max:150',
            'email' => 'required|email|unique:vh_doctors,email',
            'phone' => 'required|digits:10|unique:vh_doctors,phone',
            'consultation_fees' => 'required',
            'working_hours_start' => 'required',
            'working_hours_end' => 'required'
        );

        $validator = \Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $messages = $validator->errors();
            $response['success'] = false;
            $response['errors'] = $messages->all();
            return $response;
        }

        $response['success'] = true;
        return $response;

    }

    //-------------------------------------------------
    public static function getActiveItems()
    {
        $item = self::where('is_active', 1)
            ->withTrashed()
            ->first();
        return $item;
    }

    //-------------------------------------------------
    //-------------------------------------------------
    public static function seedSampleItems($records=100)
    {

        $i = 0;

        while($i < $records)
        {
            $inputs = self::fillItem(false);

            $item =  new self();
            $item->fill($inputs);
            $item->save();

            $i++;

        }

    }


    //-------------------------------------------------
    public static function fillItem($is_response_return = true)
    {
        $request = new Request([
            'model_namespace' => self::class,
            'except' => self::getUnFillableColumns()
        ]);
        $fillable = VaahSeeder::fill($request);
        if(!$fillable['success']){
            return $fillable;
        }
        $inputs = $fillable['data']['fill'];

        $faker = Factory::create();

        /*
         * You can override the filled variables below this line.
         * You should also return relationship from here
         */

        $specializations = [
            'Cardiology',
            'Dermatology',
            'Neurology',
            'Pediatrics',
            'Orthopedics',
            'Radiology',
            'Psychiatry',
            'Gastroenterology',
            'Endocrinology',
            'Oncology',
            'Anesthesiology',
            'Ophthalmology',
            'Urology',
            'Pulmonology',
            'Rheumatology'
        ];

        $inputs['name'] = $faker->name;
        $inputs['slug'] = $faker->slug;
        $inputs['specialization'] = $specializations[array_rand($specializations)];
        $inputs['email'] = $faker->email;
        $inputs['phone'] = $inputs['phone'] = mt_rand(1000000000, 9999999999);
        $inputs['consultation_fees'] = $faker->numberBetween(50, 500);
        $startTime = $faker->dateTimeBetween('-1 year', 'now');
        $endTime = (clone $startTime)->modify('+' . mt_rand(1, 10) . ' hours');

        $inputs['working_hours_start'] = $startTime->format('h:i A');
        $inputs['working_hours_end'] = $endTime->format('h:i A');
        $inputs['is_active'] = 1;

        if(!$is_response_return){
            return $inputs;
        }

        $response['success'] = true;
        $response['data']['fill'] = $inputs;
        return $response;
    }

    public static function availabilityUpdatedMail($inputs)
    {

        $subject = 'Availability Updated';
        $doctor = self::find($inputs['id']);

//        $patient = Patient::find($inputs['id']);

        $appointment_date_time = sprintf('%s at %s', $inputs['working_hours_start'], $inputs['working_hours_end']);

//        $email_content_for_patient = sprintf(
//            "Hi, %s\n\n
//                    We would like to inform you that the availability time for your appointment with Dr. %s has changed.\n
//                    The details of your appointment are as follows:\n\n
//                    New Appointment Date & Time: %s\n\n
//                    If you have any questions or would like to reschedule, please contact us.\n\n
//                    Regards,\n
//                    WebReinvent Technologies Pvt. Ltd.",
//
//            $patient->name,
//            $doctor->name,
//            $appointment_date_time
//        );
        $email_content_for_doctor = sprintf(
            "Hi, Dr. %s,\n
                    We would like to inform you that your availability hours have been successfully updated.\n
                    The details of your updated availability are as follows:\n\n
                    New Availability Date & Time: %s\n\n
                    If you have any questions or need to make further changes, please let us know.\n\n
                    Regards,\n
                    WebReinvent Technologies Pvt. Ltd.",
            $doctor->name,
            $appointment_date_time
        );
        VaahMail::dispatchGenericMail($subject, $email_content_for_doctor, $doctor->email);
//        VaahMail::dispatchGenericMail($subject, $email_content_for_patient, $patient->email);
    }

    public static function doctorCreatedMail($inputs)
    {
        $subject = 'Doctor Created';

        $appointment_date_time = sprintf('%s at %s', $inputs['working_hours_start'], $inputs['working_hours_end']);

        $email_content_for_doctor = sprintf(
            "Hi, Dr. %s,\n
                    We are pleased to inform you that your account has been successfully registered.\n
                    Your working hours are set as follows:\n
                    Working Hours: %s\n\n
                    If you have any questions or need further assistance, please feel free to reach out to us.\n\n
                    Regards,\n
                    WebReinvent Technologies Pvt. Ltd.",
            $inputs['name'],
            $appointment_date_time
        );
        VaahMail::dispatchGenericMail($subject, $email_content_for_doctor, $inputs['email']);
    }

    public static function doctorDeletedMail($id)
    {
        $subject = 'Doctor Deleted';

        $doctor = self::find($id);

        $appointments = Appointment::where('doctor_id', $id)->get();

        foreach($appointments as $appointment){

            $patient = Patient::find($appointment['patient_id']);
            Appointment::where('doctor_id', $id)->update(['status' => 'cancelled']);

            $appointment_date_time = sprintf('%s at %s', $appointment['date'], $appointment['time']);

            $email_content_for_patient = sprintf(
                "Hi, %s
                    We want to inform you that your appointment has been cancelled. As %s is no longer working with us.\nWe apologize for the inconvenience.\nHere are the details of your appointment:
                    Appointment Date & Time: %s
                    If you have any questions or would like to reschedule, please contact us or you can book another appointment slot [here](http://localhost:8000/backend/assignment#/appointments).
                    Regards,
                    WebReinvent Technologies Pvt. Ltd.",
                $patient->name,
                $doctor->name,
                $appointment_date_time
            );
            VaahMail::dispatchGenericMail($subject, $email_content_for_patient, $patient->email);
        }

        $doctor_working_hours = sprintf('%s to %s', $doctor['working_hours_start'], $doctor['working_hours_end']);

        $email_content_for_doctor = sprintf(
            "Hi, Dr. %s,\n
                    We would like to inform you that your account has been successfully deleted.\n
                    Please note that your working hours were previously set as follows:\n
                    Working Hours: %s\n\n
                    If you have any questions or need further assistance, please feel free to reach out to us.\n\n
                    Regards,\n
                    WebReinvent Technologies Pvt. Ltd.",
            $doctor->name,
            $doctor_working_hours
        );
        VaahMail::dispatchGenericMail($subject, $email_content_for_doctor, $doctor->email);
    }

    public static function exportDoctorData()
    {
        return Excel::download(new ExportDoctorsData,'doctors.csv');
    }

    public static function importDoctorsData(Request $request)
    {
        $doctorsData = $request->all();

//        dd($doctorsData);

        $newCount = 0;
        $updatedCount = 0;

        foreach ($doctorsData as $doctor) {
            $record = Doctor::updateOrCreate(
                ['email' => $doctor['email']],
                [
                    'name' => $doctor['name'],
                    'specialization' => $doctor['specialization'],
                    'phone' => $doctor['phone'],
                    'consultation_fees' => $doctor['consultation_fees'],
                    'is_active' => 1,
                    'working_hours_start' => $doctor['working_hours_start'],
                    'working_hours_end' => $doctor['working_hours_end']
                ]
            );

            if ($record->wasRecentlyCreated) {
                $newCount++;
            } else {
                $updatedCount++;
            }
        }

        $response['messages'][] = trans("vaahcms-general.saved_successfully", [
            'new' => $newCount,
            'updated' => $updatedCount,
        ]);

        $response['new_records'] = $newCount;
        $response['updated_records'] = $updatedCount;

        return response()->json($response);
    }

    //-------------------------------------------------
    //-------------------------------------------------
    //-------------------------------------------------


}
