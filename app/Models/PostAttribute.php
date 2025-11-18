<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAttribute extends Model
{

    //Models Information Data
    /********
     * type ==0 : Category Post
     * type ==1 : blog Category Post
     * type ==2 : Blog Tags post
     * 
     * ------------------------
     *  Status==
     * ------------------------
     * 
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * src_id        =bigint(20):null
     * parent_id     =bigint(20):null
     * reff_id       =bigint(20):null
     * type          =tinyint(1):null
     * status        =varchar(20):null
     * duration      =int(6):null
     * drag          =bigint(20):null
     * addedby_id    =bigint(20):null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/



    public function post(){
        return $this->belongsTo(Post::class,'src_id');
    }
    
    
    public function gallery(){
        return $this->belongsTo(Attribute::class,'reff_id');
    }
    
    
    
    

}
