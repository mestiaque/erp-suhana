<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggable;

class ExpenseIou extends Model
{
    use ActivityLoggable; 
    
    //Models Information Data
    /********
     * ------------------------
     *  Status==temp, active, inactive
     * ------------------------
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * name          =varchar(100):null,
     * status        =varchar(10):null
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/
     
    public function imageFile(){
    	return $this->hasOne(Media::class,'src_id')->where('src_type',10)->where('use_Of_file',1);
    }

    public function user(){
    	return $this->belongsTo(User::class,'addedby_id');
    }
    
    public function employee(){
    	return $this->belongsTo(User::class,'user_id');
    }
    
    public function employeeUser(){
    	return $this->belongsTo(User::class,'employee_id','employee_id');
    }
    
    public function branch(){
    	return $this->belongsTo(Attribute::class,'branch_id')->where('type',0);
    }
    
    public function method(){
    	return $this->belongsTo(Attribute::class,'method_id')->where('type',9);
    }
    
    public function account(){
    	return $this->belongsTo(Attribute::class,'account_id')->where('type',10);
    }
    
    public function transection(){
    	return $this->hasOne(Transaction::class,'src_id')->where('type',7);
    }
    

}
