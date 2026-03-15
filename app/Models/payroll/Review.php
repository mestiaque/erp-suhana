<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    
    //Models Information Data
    /********
     * 
     * type ==0 : review
     * type ==1 : Comments
     * 
     * ------------------------
     *  status==temp, active, inactive
     * ------------------------
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * src_id        =bigint(20):null,
     * parent_id     =bigint(20):null,
     * name          =varchar(191):null,
     * email         =varchar(191):null,
     * title         =varchar(191):null,
     * website       =varchar(191):null,
     * content       =text:null,
     * rating        =int(1):0
     * type          =int(1):0
     * status        =varchar(10):null
     * fetured       =tinyint(1):null
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/


    public function post(){
        return $this->belongsTo(Post::class,'src_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class,'addedby_id');
    }

    public function replays(){
        return $this->hasMany(Review::class,'parent_id')->where('status','<>','temp');
    }

    public function image(){
        
        if($this->user && $this->user->imageFile){
            return $this->imageFile->file_url;
        }else{
            return 'public/medies/profile.png';
        }
    }
    
}
