<?php

namespace App\Models;

use App\Models\Attribute;
use ME\Hr\Models\Attendance;
use ME\Hr\Models\EmployeeBank;
use ME\Hr\Models\EmployeeEducation;
use ME\Hr\Models\EmployeeExperience;
use ME\Hr\Models\EmployeeIncrement;
use ME\Hr\Models\EmployeeRetirement;
use ME\Hr\Models\EmployeeTraining;
use ME\Hr\Models\Leave;
use ME\Hr\Models\LeaveBalance;
use ME\Hr\Models\Probation;
use ME\Hr\Models\ProductReview;
use ME\Hr\Models\Review;
use ME\Hr\Models\Roaster;
use ME\Hr\Models\Salary;
use ME\Hr\Models\SalarySheet;
use ME\Hr\Models\SocialIdentity;
use ME\Hr\Models\Termination;
use ME\Hr\Models\UserLocation;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'distinguished_mark_bn',
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
        'present_village',
        'present_village_bn',
        'present_post_office',
        'present_post_office_bn',
        'present_upazila',
        'present_upazila_bn',
        'present_district',
        'present_district_bn',
        'permanent_address',
        'permanent_address_bn',
        'permanent_village',
        'permanent_village_bn',
        'permanent_post_office',
        'permanent_post_office_bn',
        'permanent_upazila',
        'permanent_upazila_bn',
        'permanent_district',
        'permanent_district_bn',
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
        'addedby_at' => 'datetime',
        'deleted_at' => 'datetime',
        'joining_date' => 'datetime',
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

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    public function designation(){
        return $this->belongsTo(Attribute::class,'designation_id')->where('type',2);
    }

    public function department(){
        return $this->belongsTo(Attribute::class,'department_id')->where('type',3);
    }

    public function loans(){
        return $this->hasMany(Transaction::class,'user_id')->where('type',2);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'buyer_id', 'id');
    }

    public function salaries(){
        return $this->hasMany(Salary::class,'user_id');
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



    // public function sales(){
    //     return $this->hasMany(Order::class,'addedby_id')->where('order_type','sale_invoices')->where('order_status','confirmed');
    // }

    public function comments(){
        return $this->hasMany(Review::class,'addedby_id')->where('type',1);
    }

    public function reviews(){
        return $this->hasMany(ProductReview::class);
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
            return 'medies/profile.png';
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
            return 'app-assets/images/carousel/22.jpg';
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

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id')->orderBy('id','desc');
    }
    public function duePurchaseAmount()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id')->where('status','approved')->where('due_amount','>',0)->sum('due_amount');
    }

    public function accounts()
    {
        return $this->hasMany(Attribute::class, 'addedby_id')->where('type', 10);
    }

    public function creditorBill()
    {
        return $this->hasMany(CreditorBill::class, 'creditor_id');
    }

        // User model
    public function is_staff()
    {
        return $this->staff == 1;
    }

    public function is_admin()
    {
        return $this->admin ==  1;
    }

    public function scopeFilterByType($query, $type)
    {
        return match ($type) {

            'supplier' => $query->where([
                'supplier'     => true,
                'buyer'        => false,
                'staff'        => false,
                'admin'        => false,
                'customer'     => false,
                'merchandiser' => false,
            ]),

            'buyer' => $query->where([
                'supplier'     => false,
                'buyer'        => true,
                'staff'        => false,
                'admin'        => false,
                'customer'     => false,
                'merchandiser' => false,
            ]),

            'staff' => $query->where([
                'supplier'     => false,
                'buyer'        => false,
                'staff'        => true,
                'admin'        => false,
                'customer'     => false,
                'merchandiser' => false,
            ]),

            'admin' => $query->where([
                'supplier'     => false,
                'buyer'        => false,
                'staff'        => false,
                'admin'        => true,
                'customer'     => false,
                'merchandiser' => false,
            ]),

            'customer' => $query->where([
                'supplier'     => false,
                'buyer'        => false,
                'staff'        => false,
                'admin'        => true,
                'customer'     => true,
                'merchandiser' => false,
            ]),

            'employee' => $query->where([
                'supplier'     => false,
                'buyer'        => false,
                'staff'        => false,
                'admin'        => false,
                'customer'     => true,
                'merchandiser' => false,
            ]),

            'merchandiser' => $query->where([
                'supplier'     => false,
                'buyer'        => false,
                'staff'        => false,
                'admin'        => true,
                'customer'     => true,
                'merchandiser' => true,
            ]),

            default => $query
        };
    }


    // example: $user->setTypes('merchandiser');
    public function setTypes($type)
    {
        // Reset all flags
        $this->supplier     = false;
        $this->buyer        = false;
        $this->staff        = false;
        $this->admin        = false;
        $this->customer     = false;
        $this->merchandiser = false;

        match ($type) {
            'supplier' => $this->supplier = true,
            'buyer'    => $this->buyer = true,
            'staff'    => $this->staff = true,
            'admin'    => $this->admin = true,
            'employee' => $this->customer = true,

            'customer' => [
                $this->customer = true,
                $this->admin    = true,
            ],

            'merchandiser' => [
                $this->merchandiser = true,
                $this->customer     = true,
                $this->admin        = true,
            ],
        };

        return $this;
    }



    public function hasPermission($permission)
    {

        // $permission must be like: expenses.edit
        list($module, $action) = explode('.', $permission);

        // Get user permission JSON
        $permissions = json_decode($this->permission->permission ?? '{}', true);

        // Module exists?
        if (!isset($permissions[$module])) {
            return false;
        }

        // Child permission exists and is ON?
        return isset($permissions[$module][$action])
            && in_array($permissions[$module][$action], ['on', '1', 1, true], true);
    }


    public function getAvt($size = 40)
    {
        if ($this->image()) {
            return '<img src="'.asset($this->image()).'"
                    alt="'.$this->name.'"
                    class="rounded-circleX"
                    style="width: '.$size.'px; height: '.$size.'px; object-fit: cover; margin-right: 10px;">';
        }

        return '<div class="rounded-circleX d-flex align-items-center justify-content-center text-white font-weight-bold"
                    style="width: '.$size.'px; height: '.$size.'px; background-color: '.random_color($this->id ?? 0).'; margin-right: 10px;">
                    '.strtoupper(substr($this->name ?? 'U', 0, 1)).'
                </div>';
    }

    public function otherInfo()
    {
        $current = $this->other_information;
        if (is_array($current)) {
            return $current;
        }

        $decoded = json_decode((string) $current, true);
        return is_array($decoded) ? $decoded : [];
    }



}
