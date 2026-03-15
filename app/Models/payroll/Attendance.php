<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //Models Information Data
    /********
     *
     * Column:
     *
     * id               =bigint(20):None,
     * name             =varchar(250):null,
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     *
     *
     ****/

     protected $casts = [
        'in_time' => 'datetime',
        'out_time' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'in_time',
        'out_time',
        'in_minutes',
        'overtime_minutes',
        'latitude',
        'longitude',
        'status',
        'via',
        'verify_type',
        'device_sn',
        'created_at',
        'updated_at',
        'date',
        'work_hour',
        'late_time',
        'early_out',
        'overtime',
        'location_lat',
        'location_long',
        'remarks'
    ];


    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }

    public function machinery(){
        return $this->hasMany(CompanyMachinery::class,'company_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('hideUser7', function (Builder $builder) {
            $builder->where('user_id', '!=', 7);
        });
    }

}
