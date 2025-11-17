<?php

namespace App\Http\Controllers\Customer;

use Mail;
use Auth;
use Hash;
use Session;
use Response;
use Cookie;
use Str;
use File;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    
      
    public function __construct(){

        function isMobileDevice() {
          return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo 
        |fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i" 
        , $_SERVER["HTTP_USER_AGENT"]); 
        }

        if(isMobileDevice())
        {
          $this->device =general()->theme.'.customer.';
        }
        else
        {
          $this->device =general()->theme.'.customer.';
        }

    }

    public function dashboard(Request $request){
        return 'login success';
        return view($this->device.'dashboard');

    }

    public function profileEdit(){
      return view($this->device.'profileEdit');
    }


    public function profileUpdate(Request $r){
      
        $myprofile =Auth::user();
        $check = $r->validate([
            'name' => 'required|max:50',
            'address' => 'nullable|max:191',
            'division' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'city' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        if(!$check){
            Session::flash('error','Need To validatation');
            return back();
        }

        $myprofile->name =$r->name;
        $myprofile->division =$r->division;
        $myprofile->district =$r->district;
        $myprofile->city =$r->city;
        $myprofile->address_line1 =$r->address;
        
        $addr =$myprofile->address_line1;
        $city =Country::find($myprofile->city);
        if($city){
         $addr .=', '.$city->name;
        }
        
        $dis =Country::find($myprofile->district);
        if($dis){
         $addr .=', '.$dis->name;
        }
        
        $div =Country::find($myprofile->division);
        if($div){
         $addr .=', '.$div->name;
        }
        
       $myprofile->full_address=$addr;
        
        ///////Image Uploard Start////////////
      if($r->hasFile('image')){
            $file =$r->image;
            $src  =$myprofile->id;
            $srcType  =6;
            $fileUse  =1;
          
            uploadFile($file,$src,$srcType,$fileUse);
      }
      
      ///////Image Uploard End////////////
        
      $myprofile->save();


      Session::flash('success','Your Are Successfully Done');
      return redirect()->back();

    }
    
    

    public function changePassword(){
      return view($this->device.'changePassword');
    }

    public function changePasswordUpdate(Request $r){
       $user = Auth::user();

        $check = $r->validate([
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if(!$check){
            Session::flash('error','Need To validatation');
            return redirect()->back();
        }
        
        if(Hash::check($r->current_password, $user->password)){
          $user->password_show=$r->password;
          $user->password=Hash::make($r->password);
          $user->save();
          Session()->flash('success','Your Are Successfully Done');
          return redirect()->back();
        }else{
        Session()->flash('error','Carrent Password Are Not Match');
        return redirect()->back();
        }

    }


    


}
