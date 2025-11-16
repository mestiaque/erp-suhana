<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    
	//Models Information Data
    /********
     * file_type ==0 : unknown
     * file_type ==1 : image
     * file_type ==2 : pdf
     * file_type ==3 : doc
     * file_type ==4 : Zip,rar
     * file_type ==5 : Vedio
     * file_type ==6 : audio
     * 
     * 
     * use_Of_file ==0 : media,
     * use_Of_file ==1 : image,
     * use_Of_file ==2 : banner,
     * use_Of_file ==3 : gallery,
     * use_Of_file ==4 : icon,
     * 
     * 
     * src_type  ==0 : media
     * src_type  ==1 : post
     * src_type  ==2 : category
     * src_type  ==3 : attribute
     * src_type  ==4 : Menus
     * src_type  ==5 : review
     * src_type  ==6 : Users
     * src_type  ==7 : General
     * 
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * src_id        =bigint(20):null,
     * src_type      =tinyint(1):0,
     * use_Of_file   =tinyint(1):0,
     * file_name     =varchar(255):null,
     * alt_text      =varchar(255):null,
     * caption       =varchar(255):null,
     * description   =text:null,
     * file_url      =varchar(191):null
     * file_size     =varchar(100):null
     * file_type     =int(1):0
     * drag          =int(5):0
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/

	public function image(){
    	
		if($this->file_url){
			if($this->file_type==1){
			return $this->file_url;
			}elseif($this->file_type==2){
			return 'public/medies/defultpdf.png';
			}elseif($this->file_type==3){
			return 'public/medies/defultdocx.png';
			}elseif($this->file_type==4){
			return 'public/medies/defultzip.png';
			}elseif($this->file_type==5){
			return 'public/medies/defultvedio.png';
			}else{
			return 'public/medies/defultunknown.png';
			}
		}else{
			return 'public/medies/noimage.jpg';
		}

    }
    
    
    public function imageName(){
        if($this->file_rename){
            return $this->file_rename;
        }else{
            return 'noimage.jpg';
        }
    }
    
    public function user(){
    	return $this->belongsTo(User::class,'addedby_id');
    }
    

}

