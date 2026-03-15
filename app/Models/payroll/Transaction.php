<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //Models Information Data
    /********
     * 
     * 
     * Column:
     * 
     * id               =bigint(20):None,
     * user_id          =bigint(20):null,
     * provider_name    =varchar(255):null,
     * provider_id      =varchar(255):null,
     * provider_token   =varchar(255):null,
     * provider_img_url =varchar(255):null,
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     * 
     * 
     * 
     ****/
     
    public function imageFile(){
    	return $this->hasOne(Media::class,'src_id')->where('src_type',9)->where('use_Of_file',1);
    }
    
    public function method(){
    	return $this->belongsTo(Attribute::class,'src_id');
    }
  
    public function accountMethod(){
    	return $this->belongsTo(Attribute::class,'account_id');
    }
    
    public function paymentMethod(){
    	return $this->belongsTo(Attribute::class,'payment_method_id');
    }
    
    public function sale(){
    	return $this->belongsTo(Order::class,'src_id');
    }
    public function user(){
    	return $this->belongsTo(User::class,'user_id');
    }
    
    public function company(){
    	return $this->belongsTo(Company::class,'user_id');
    }
    
    public function traddingBill(){
    	return $this->belongsTo(SupplierTrading::class,'src_id');
    }
    
    public function assinee(){
    	return $this->belongsTo(User::class,'addedby_id');
    }

    
}
