<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{

    //Models Information Data
    /********
     * ------------------------
     *  Status==temp, active, inactive
     * ------------------------
     *
     * Column:
     *
     * id            =bigint(20):None,
     * user_id          =varchar(100):null,
     * salary_amount =floot(10,2):0.00,
     * status        =varchar(10):null
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     *
     *
     *
     ****/

    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('hideUser7', function (Builder $builder) {
            // $builder->where('user_id', '!=', 7);
            $builder->filterBy('employee');
        });
    }


}
