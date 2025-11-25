<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancelItems()
    {
        return $this->hasMany(OrderReturnItem::class,'order_id')->where('return_type',false);
    }

    public function returnItems()
    {
        return $this->hasMany(OrderReturnItem::class,'order_id')->where('return_type',true);
    }

    public function hasLcOrders()
    {
        return $this->hasMany(OrderItem::class,'src_id')->whereHas('order', function($query) {
                    $query->where('order_status', 'confirmed');
                });
    }

    public function hasLcOrder()
    {
        return $this->hasOne(OrderItem::class,'src_id');
    }

    public function branch(){
        return $this->belongsTo(Attribute::class,'branch_id')->where('type',13);
    }

    public function bank(){
        return $this->belongsTo(Attribute::class,'bank_id')->where('type',9);
    }

    public function marchantize(){
        return $this->belongsTo(Attribute::class,'marchantizer_id')->where('type',4);
    }

    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }

    public function hasSubInvoices(){
        return $this->hasMany(Order::class,'parent_id');
    }

    function incrementNumberInString() {
        $prefix =null;
        $newLastNumber =null;
        if($this->marchantize && $this->company){
            $prefix .=$this->marchantize->slug.'/'.$this->company->slug.'-';
        }

        if($prefix){
            $inv = Order::where('id','<>',$this->id)->where('parent_id',null)->where('order_type','pi_invoices')
                    ->where('invoice','like','%'.$prefix.'%')
                    //->orderBy('invoice','desc')
                    ->count();

            // if($inv){
                $newLastNumber=(int)$inv+1;
                // if($lastNumber){
                    // return $newLastNumber;
                    if($newLastNumber < 10) {
                        $newLastNumber =$prefix.'0' .(string)$newLastNumber;
                    }else{
                        $newLastNumber =$prefix.(string)$newLastNumber;
                    }
                // }
            // }else{
            //     $newLastNumber =$prefix.'01';
            // }
        }
        return $newLastNumber;
    }


    public function makeInvoice(){

        $invoice =null;

        if($this->marchantize){
           $invoice .=$this->marchantize->slug;
        }

        if($this->company){
           $invoice .='/'.$this->company->slug;
        }

        $invoice .='-'.$this->id;

        return $invoice;
    }

    public function TotalGramUnit(){

        $gram =0;

        $un='';

        foreach($this->items as $item){

            if($item->product){
                if($item->product->weight_unit=='gram'){
                    $gram+=  $item->quantity*$item->product->weight_amount;
                }
            }

        }

        return $gram;

    }

    public function TotalLiterUnit(){
        $ml =0;
        foreach($this->items as $item){
            if($item->product){
               if($item->product->weight_unit=='ml'){
                   $ml+=$item->product->weight_amount?$item->product->weight_amount*$item->quantity:0;
               }
            }

        }

        return $ml;
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function posByuser(){
        return $this->belongsTo(User::class,'pending_by');
    }

    public function saleBy(){
        return $this->belongsTo(User::class,'addedby_id');
    }

     public function customerDelivery(){
        return $this->belongsTo(User::class,'order_delivery_By');
    }

    public function transections()
    {
        return $this->hasMany(Transaction::class,'src_id')->whereIn('type',[0,3,8])->where('status','<>','temp');
    }

    public function transectionsAll()
    {
        return $this->hasMany(Transaction::class,'src_id')->where('status','<>','temp');
    }

    public function transectionsTemp()
    {
        return $this->hasMany(Transaction::class,'src_id')->whereIn('type',[0,3])->where('type',3)->where('status','temp');
    }

    public function transectionsSuccess()
    {
        return $this->hasMany(Transaction::class,'src_id')->where('status','success');
    }

    public function transectionsRefund()
    {
        return $this->hasMany(Transaction::class,'src_id')->whereIn('type',[2])->where('status','success');
    }


    public function returnTransections()
    {
        return $this->hasMany(Transaction::class,'order_id')->where('type',2);
    }

    public function countryN(){
        return $this->belongsTo(Country::class,'country');
    }

    public function divitionN(){
        return $this->belongsTo(Country::class,'division');
    }

    public function districtN(){
        return $this->belongsTo(Country::class,'district');
    }


    public function cityN(){
        return $this->belongsTo(Country::class,'city');
    }


    public function fullAddress(){

        $addr =$this->address;
        if($this->city_name){
           $addr .=', '.$this->city_name;
        }

        if($this->districtN){
           $addr .=', '.$this->districtN->name;
        }
        if($this->postal_code){
           $addr .=$this->postal_code;
        }
        return $addr;

    }



}
