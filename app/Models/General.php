<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class General extends Model
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
     * title            =varchar(100):null,
     * subtitle         =varchar(200):null,
     * website          =varchar(100):null,
     * logo             =varchar(100):null,
     * favicon          =varchar(100):null,
     * mobile           =varchar(100):null,
     * email            =varchar(100):null,
     * address_one      =text():null,
     * address_two      =text():null,
     * postal_address   =varchar(191):null,
     * postal_code      =varchar(10):null,
     * city             =varchar(50):null,
     * state            =varchar(50):null,
     * division         =varchar(50):null,
     * country          =varchar(50):null,
     * commingsoon_mode =tinyint(1):null,
     * notification_status =tinyint(1):null,
     * fb_pageId        =varchar(100):null,
     * meta_keyword     =text():null,
     * meta_description =varchar(200):null,
     * meta_author      =varchar(100):null,
     * meta_title       =varchar(200):null,
     * script_head      =longtext():null
     * script_body      =longtext():null
     * custom_css       =longtext():null
     * custom_js        =longtext():null
     * copyright_text   =text():null
     * mail_driver      =varchar(100):null,
     * mail_host        =varchar(100):null,
     * mail_port        =varchar(100):null,
     * mail_username    =varchar(100):null,
     * mail_password    =varchar(100):null,
     * mail_encryption  =varchar(100):null,
     * mail_from_name   =varchar(100):null,
     * mail_from_address=varchar(100):null,
     * mail_status      =tinyint(1):0;
     * sms_username     =varchar(50):null,
     * sms_password     =varchar(50):null,
     * sms_senderid     =varchar(50):null,
     * sms_url_masking  =varchar(200):null,
     * sms_url_nonmasking=varchar(200):null,
     * sms_type         =varchar(50):null,
     * sms_status       =tinyint(1):0,
     * admin_numbers    =text():null,
     * fb_app_id        =varchar(100):null,
     * fb_app_secret    =varchar(100):null,
     * fb_app_redirect_url=varchar(200):null,
     * tw_app_id        =varchar(100):null,
     * tw_app_secret    =varchar(100):null,
     * tw_app_redirect_url=varchar(200):null,
     * google_client_id =varchar(100):null,
     * google_client_secret=varchar(100):null,
     * google_client_redirect_url=varchar(200):null,
     * facebook_link    =varchar(200):null,
     * twitter_link     =varchar(200):null,
     * instagram_link   =varchar(200):null,
     * linkedin_link    =varchar(200):null,
     * pinterest_link   =varchar(200):null,
     * youtube_link     =varchar(200):null,
     * currency         =varchar(200):null,
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * created_at       =timestamp:null
     * updated_at       =timestamp:null
     * 
     * 
     ****/


    public function logo()
    {
        if($this->logo)
        {
            return $this->logo;
        }else{
            return 'public/medies/no-logo.png';
        }
    }

    public function favicon()
    {
        if($this->favicon)
        {
            return $this->favicon;
        }else{
            return 'public/medies/no-favicon.png';
        }
    }
    
    public function signature()
    {
        if($this->signature)
        {
            return $this->signature;
        }else{
            return 'public/medies/noimage.jpg';
        }
    }


}
