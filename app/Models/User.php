<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasAudit;

class User extends Authenticatable
{
    use HasAudit;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'permission_id',
        'name',
        'email',
        'mobile',
        'employee_id',
        'signature',
        'status',
        'admin',
        'staff',
        'email_verified_at',
        'password',
        'password_show',
        'remember_token',
        'addedby_id',
        'addedby_at',
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
    ];

    public function permission(){
        return $this->belongsTo(Permission::class);
    }

    public function addedBy(){
        return $this->belongsTo(User::class,'addedby_id');
    }

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }

    public function scopeHideDev($query)
    {
        $hiddenIds = [7]; // যেগুলো hide করতে চাও
        return $query->whereNotIn('id', $hiddenIds);
    }

    // public function sales(){
    //     return $this->hasMany(Order::class,'addedby_id')->where('order_type','sale_invoices')->where('order_status','confirmed');
    // }

    public function imageFile(){
    	return $this->hasOne(File::class,'fileable_id')->where('fileable_type',self::class)->where('use_case','profile');
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
            return asset('medies/profile.png');
        }
    }

    public function signatureUrl(): ?string
    {
        $file = $this->hasOne(File::class,'fileable_id')->where('fileable_type',self::class)->where('use_case','signature')->first();
        if ($file) {
            return $file->file_url;
        }

        return $this->signature ? asset($this->signature) : null;
    }

    public function imageName(){

        if($this->imageFile){
            return $this->imageFile->file_rename;
        }else{
            return 'noimage.jpg';
        }
    }

    public function bannerFile(){
        return $this->hasOne(File::class,'fileable_id')->where('fileable_type',self::class)->where('use_case','banner');
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
        return $this->hasMany(File::class,'fileable_id')->where('fileable_type',self::class)->where('use_case','gallery');
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
            'admin' => $query->where(['admin' => true, 'staff' => false]),
            'staff' => $query->where(['admin' => false, 'staff' => true]),
            default => $query
        };
    }

    // example: $user->setTypes('admin');
    public function setTypes($type)
    {
        $this->admin = $type === 'admin';
        $this->staff = $type === 'staff';

        return $this;
    }

    public function hasPermission($permission)
    {

        // $permission must be like: expenses.edit
        list($module, $action) = explode('.', $permission);

        // Get user permission JSON
        $permissions = json_decode($this->permission->permission ?? '{}', true);

        // Developer role (999) auto-inherits whatever a Super Admin checked in
        // Developer Permissions — those modules/actions are hidden from the Role
        // Update checklist entirely, so this is the only way to grant them.
        if ((int) $this->permission_id === 999) {
            $general = General::first();
            if ($general) {
                $permissions = array_replace_recursive($permissions ?: [], $general->developerGrantedPermissions());
            }
        }

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

}
