<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    
    //Models Information Data
    /********
     * 
     * type ==0 : Page
     * 
     * ------------------------
     *  Status==
     * ------------------------
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * user_id       =bigint(20):null,
     * latitude      =bigint(20):null,
     * longitude     =bigint(20):null,
     * visit_url     =bigint(20):null,
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/
    

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
     
     
     

}
