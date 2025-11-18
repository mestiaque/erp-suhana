<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public function order(){
            return $this->belongsTo(Order::class);
    }
        
    public function branch(){
            return $this->belongsTo(Attribute::class,'branch_id');
    }

    public function contributor(){
        return $this->belongsTo(UserInfo::class,'contributor_id');
    }

    public function piOrder(){
        return $this->belongsTo(Order::class,'src_id');
    }
    
    public function product(){
        return $this->belongsTo(Post::class,'src_id')->where('type',3);
    }

    public function itemAnswer(){
        return $this->hasMany(TutorialAnswer::class,'item_id');
    }
    
    public function itemPrice(){
        $numberString =(string)$this->price;
         $numberString = rtrim($numberString, '0');
        return $numberString;
    }

}
