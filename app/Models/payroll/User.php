<?php

namespace App\Models;

use App\Http\Controllers\Admin\RetirementController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\EmployeeInfo;
use App\Models\EmployeeEducation;
use App\Models\EmployeeTraining;
use App\Models\EmployeeExperience;
use App\Models\EmployeeBank;
use App\Models\Roaster;
use App\Models\LeaveBalance;
use App\Models\SalarySheet;
use App\Models\EmployeeIncrement;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The "booted" method of the model.
     */

    protected $fillable = [
        'permission_id',
        'name',
        'bn_name',
        'email',
        'mobile',
        'profile',
        'photo',
        'signature',
        'address_line1',
        'address_line2',
        'postal_address',
        'location',
        'postal_code',
        'city',
        'district',
        'division',
        'country',
        'dob',
        'gender',
        'grade_lavel',
        'marital_status',
        'designation_id',
        'division_id',
        'section_id',
        'shift_id',
        'line_number',
        'report_to',
        'father_name',
        'father_name_bn',
        'mother_name',
        'mother_name_bn',
        'spouse_name',
        'spouse_name_bn',
        'boys',
        'girls',
        'blood_group',
        'religion',
        'education',
        'work_type',
        'birth_registration',
        'passport_no',
        'driving_license',
        'etin',
        'distinguished_mark',
        'height',
        'weight',
        'home_district',
        'nationality',
        'emergency_mobile',
        'emergency_relation',
        'other_information',
        'reference_1',
        'reference_2',
        'nominee',
        'nominee_bn',
        'nominee_relation',
        'nominee_age',
        'present_address',
        'present_address_bn',
        'permanent_address',
        'permanent_address_bn',
        'salary_type',
        'employee_id',
        'employee_type',
        'department_id',
        'employment_status',
        'employee_status',
        'nid_number',
        'login_status',
        'status',
        'fetured',
        'email_verified_at',
        'password',
        'password_show',
        'remember_token',
        'reset_remember',
        'api_token',
        'device_key',
        'verify_code',
        'verify_code_status',
        'gross_salary',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'transport_allowance',
        'food_allowance',
        'conveyance_allowance',
        'provident_fund',
        'balance',
        'subscriber',
        'customer',
        'supplier',
        'engineer',
        'admin',
        'super_admin',
        'latitude',
        'longitude',
        'addedby_id',
        'addedby_at',
        'exited_at',
        'created_at',
        'joining_date',
        'confirmation_date',
        'retirement_date',
        'updated_at',
    ];



    protected $hidden = [
        'password',
        'remember_token',
        'password_show',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function identities() {
       return $this->hasMany(SocialIdentity::class);
    }

    public function permission(){
        return $this->belongsTo(Permission::class);
    }

    public function addedBy(){
        return $this->belongsTo(User::class,'addedby_id');
    }

    public function designation(){
        return $this->belongsTo(Attribute::class,'designation_id')->where('type',2);
    }

    public function department(){
        return $this->belongsTo(Attribute::class,'department_id')->where('type',3);
    }

    public function employeeType(){
        return $this->belongsTo(Attribute::class,'employee_type')->where('type',16);
    }

    // Division (type = 11)
    public function divisionData()
    {
        return $this->belongsTo(Attribute::class, 'division')->where('type', 11);
    }

    // Grade (type = 12)
    public function grade()
    {
        return $this->belongsTo(Attribute::class, 'grade_lavel')->where('type', 12);
    }

    // Line Number (type = 13)
    public function line()
    {
        return $this->belongsTo(Attribute::class, 'line_number')->where('type', 13);
    }

    // Section (type = 14)
    public function section()
    {
        return $this->belongsTo(Attribute::class, 'section_id')->where('type', 14);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function loans(){
        return $this->hasMany(Transaction::class,'user_id')->where('type',2);
    }

    public function salaries(){
        return $this->hasMany(Salary::class,'user_id');
    }

    public function engineers(){
        return $this->hasMany(Attribute::class,'addedby_id')->where('type',0)->where('status','<>','temp');
    }

    public function comments(){
        return $this->hasMany(Review::class,'addedby_id')->where('type',1);
    }

    public function imageFile(){
    	return $this->hasOne(Media::class,'src_id')->where('src_type',6)->where('use_Of_file',1);
    }

    public function image($type=null){

        if($this->imageFile){
            if($type=='sm'){
               return $this->imageFile->file_url_sm;
            }elseif($type=='md'){
               return $this->imageFile->file_url_md;
            }elseif($type=='lg'){
               return $this->imageFile->file_url_lg;
            }else{
               return $this->imageFile->file_url;
            }
        }else{
            return false;
            // return 'public/medies/profile.png';
        }
    }

    public function imageName(){

        if($this->imageFile){
            return $this->imageFile->file_rename;
        }else{
            return 'noimage.jpg';
        }
    }


    public function bannerFile(){
        return $this->hasOne(Media::class,'src_id')->where('src_type',6)->where('use_Of_file',2);
    }

    public function banner(){

        if($this->bannerFile){
            return $this->bannerFile->file_url;
        }else{
            return 'public/app-assets/images/carousel/22.jpg';
        }
    }

    public function bannerName(){

        if($this->bannerFile){
            return $this->bannerFile->file_rename;
        }else{
            return 'no-banner.png';
        }
    }

    public function galleryFiles(){
        return $this->hasMany(Media::class,'src_id')->where('src_type',6)->where('use_Of_file',3);
    }

    public function countryN(){
        return $this->belongsTo(Country::class,'country');
    }

    public function divitionN(){
        return $this->belongsTo(Country::class,'division');
    }

    public function districtN(){
        return $this->belongsTo(Country::class,'district');
    }


    public function cityN(){
        return $this->belongsTo(Country::class,'city');
    }


    public function user(){
        return $this->belongsTo(User::class,'id');
    }

    public function fullAddress(){

        $addr =$this->address_line1;

        if($this->cityN){
           $addr .=', '.$this->cityN->name;
        }

        if($this->districtN){
           $addr .=', '.$this->districtN->name;
        }

        if($this->postal_code){
           $addr .=' - '.$this->postal_code;
        }

        if($this->divitionN){
           $addr .=', '.$this->divitionN->name;
        }

        return $addr;

    }

    public function posts(){
        return $this->hasMany(Post::class,'addedby_id')->where('type',1);;
    }


    public function lastLocation(){
    	return $this->hasOne(UserLocation::class,'user_id');
    }


    public function scopeHideDev($query)
    {
        $hiddenIds = [7]; // যেগুলো hide করতে চাও
        return $query->whereNotIn('id', $hiddenIds);
    }

    public function employeeEducation()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employeeTraining()
    {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function employeeExperience()
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function employeeBankInfo()
    {
        return $this->hasMany(EmployeeBank::class);
    }

    public function roasters()
    {
        return $this->hasMany(Roaster::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function salarySheets()
    {
        return $this->hasMany(SalarySheet::class);
    }

    public function increments()
    {
        return $this->hasMany(EmployeeIncrement::class);
    }

    public function terminations()
    {
        return $this->hasMany(Termination::class);
    }

    public function retirements()
    {
        return $this->hasMany(EmployeeRetirement::class);
    }

    public function probations()
    {
        return $this->hasMany(Probation::class);
    }

    // ========================
    // Role Helper Functions
    // ========================

    /**
     * Check if user is an Admin (admin = true)
     */
    public function isAdmin()
    {
        return $this->admin == true;
    }

    /**
     * Check if user is a Super Admin (super_admin = true)
     */
    public function isSuperAdmin()
    {
        return $this->super_admin == true;
    }

    /**
     * Check if user is an Employee/Customer (customer = true)
     */
    public function isEmployee()
    {
        return $this->customer == true;
    }

    public function scopeFilterBy($query, $type = 'employee')
    {
        if ($type == 'admin') {
            // only admin true and customer false
            return $query->where('admin', true)
                        ->where('customer', false);
        } elseif ($type == 'employee') {
            // customer true (admin true or false both allowed)
            return $query->where('customer', true);
        }

        return $query;
    }

    /**
     * Generate a random password
     */
    public static function generatePassword($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }
        return $password;
    }

    /**
     * Create a new Admin user
     */
    public static function createAdmin($name, $email)
    {
        $password = self::generatePassword();

        $user = self::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'password_show' => $password,
            'admin' => true,
            'super_admin' => false,
            'customer' => false,
            'status' => 1,
        ]);

        return ['user' => $user, 'password' => $password];
    }

    /**
     * Create a new Super Admin user
     */
    public static function createSuperAdmin($name, $email)
    {
        $password = self::generatePassword();

        $user = self::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'password_show' => $password,
            'admin' => true,
            'super_admin' => true,
            'customer' => false,
            'status' => 1,
        ]);

        return ['user' => $user, 'password' => $password];
    }

    /**
     * Create a new Employee user
     */
    public static function createEmployee($name, $employeeId)
    {
        $password = self::generatePassword();

        $user = self::create([
            'name' => $name,
            'employee_id' => $employeeId,
            'password' => bcrypt($password),
            'password_show' => $password,
            'admin' => false,
            'customer' => true,
            'status' => 1,
        ]);

        return ['user' => $user, 'password' => $password];
    }


    public function getAvt($size = 40)
    {
        if ($this->image()) {
            return '<img src="'.asset($this->image()).'"
                    alt="'.$this->name.'"
                    class="rounded-circle"
                    style="width: '.$size.'px; height: '.$size.'px; object-fit: cover; margin-right: 10px;">';
        }

        return '<div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                    style="width: '.$size.'px; height: '.$size.'px; background-color: '.random_color($this->id ?? 0).'; margin-right: 10px;">
                    '.strtoupper(substr($this->name ?? 'U', 0, 1)).'
                </div>';
    }

    public function getFirstLetter()
    {
        return strtoupper(substr($this->name ?? 'U', 0, 1));
    }

    public function getStatus()
    {
        if(Termination::where('user_id', $this->id)->where('status', 'approved')->latest()->first()){
            return 'terminated';
        }elseif(EmployeeRetirement::where('user_id', $this->id)->where('status', 'approved')->latest()->first()){
            return 'retired';
        }elseif(Probation::where('user_id', $this->id)->where('status', 'approved')->latest()->first()){
            return 'probation';
        }elseif(User::where('id', $this->id)->where('status', 1)->first()){
            return 'active';
        }else{
            return 'unknown';
        }
    }

    public function getEmployeeStatusAttribute()
    {
        $latestTermination = Termination::where('user_id', $this->id)->where('status', 'approved')->latest()->first();
        $latestRetirement = EmployeeRetirement::where('user_id', $this->id)->where('status', 'approved')->latest()->first();
        $latestProbation  = Probation::where('user_id', $this->id)->latest()->first(); // remove 'approved' if needed

        if ($latestTermination) {
            return 'terminated';
        } elseif ($latestRetirement) {
            return 'retired';
        } elseif ($latestProbation) { // check properly
            return 'probation';
        } elseif ($this->status == 1) {
            return 'active';
        } elseif ($this->status == 0) {
            return 'inactive';
        } else {
            return 'unknown';
        }
    }
}

