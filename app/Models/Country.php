<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //Models Information Data
    /********
     * type ==0 : null
     * type ==1 : country
     * type ==2 : division
     * type ==3 : district
     * type ==4 : thana
     * 
     * 
     * Column:
     * 
     * id               =bigint(20):None,
     * name             =varchar(250):null,
     * bn_name          =varchar(100):null,
     * type             =int(3):null,
     * parent_id        =int(11):null,
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     * 
     * 
     ****/
    
    public function subDatas(){
        return $this->hasMany(Country::class,'parent_id');
    }
}
