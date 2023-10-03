<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CompanyTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Shopconfig;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ShopconfigController extends Controller
{
    /**
     * Display legal information of the site.
     */
    public function showLegal()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('legal_info')->value ?? null]);
    }

    /**
     * Create or update legal information in storage.
     */
    public function insertLegal(Request $request)
    {
        $request->validate([
           'company_name'=>['required','string','max:255'],
           'company_type'=>['required','string','max:50',new Enum(CompanyTypeEnum::class)],
           'national_identity_number'=>['required','numeric','digits:11,11'],
           'registration_number'=>['required','numeric'],
           'economic_number'=>['required','numeric','digits:12,12'],
           'address'=>['required','string','max:400'],
           'postal_code'=>['required','numeric','digits:10,10'],
           'fax'=>['required','numeric','digits:11,11'],
           'phone'=>['required','numeric'],
           'signature'=>['required','regex:/data:image\\/(jpeg|jpeg|png)/i'],
        ]);
        $this->setAuthorize(Shopconfig::getValue('legal_info'));
        $result = Shopconfig::updateOrCreate(['option'=>'legal_info'],['value'=>json_encode($request->except('option'))]);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display store detail of the site.
     */
    public function showStoreDetail()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('store_detail')->value ?? null]);
    }

    /**
     * Create or update store detail in storage.
     */
    public function insertStoreDetail(Request $request)
    {
        $request->validate([
            'shop_name'=>['required','string','max:255'],
            'shop_name_ir'=>['required','string','max:255'],
            'shop_url'=>['required','string','max:255'],
            'shop_front_url'=>['required','string','max:255'],
            'shop_address'=>['required','string','max:400'],
            'support_phone'=>['required','numeric'],
            'email'=>['required','string','email','max:255'],
            'enamad'=>['nullable','string','max:500'],
            'samandehi'=>['nullable','string','max:500'],
            'mojavez'=>['nullable','string','max:500'],
            'logo'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],
            'favicon'=>['required','regex:/data:image\\/(jpeg|jpeg|png|ico)/i'],
            'footer_logo'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],
            'copy_right'=>['required','string','max:400'],
            'shop_bio'=>['required','string','max:10000'],
            'comment_rule'=>['required','string','max:10000'],
        ]);
        $host = parse_url($request->shop_url, PHP_URL_HOST);
        $this->setAuthorize(Shopconfig::getValue('store_detail'));
        $result = Shopconfig::updateOrCreate(['option'=>'store_detail'],['value'=>json_encode($request->except('option'))]);
        setEnvironmentValue([
            'APP_NAME'=>"'".$request->shop_name."'",
            'APP_URL'=>$request->shop_url,
            'FRONT_URL'=> str_ends_with($request->shop_front_url, '/') ? $request->shop_front_url : $request->shop_front_url.'/',
            'SANCTUM_STATEFUL_DOMAINS'=>$host,
            'SESSION_DOMAIN'=>".{$host}",
        ]);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display social media of the site.
     */
    public function showSocialMedia()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('social_media')->value ?? null]);
    }

    /**
     * Create or update social media in storage.
     */
    public function insertSocialMedia(Request $request)
    {
        $request->validate([
            'telegram'=>['nullable','string','max:255'],
            'whatsapp'=>['nullable','string','max:255'],
            'instagram'=>['nullable','string','max:255'],
            'facebook'=>['nullable','string','max:255'],
            'twitter'=>['nullable','string','max:255'],
        ]);
        $this->setAuthorize(Shopconfig::getValue('social_media'));
        $result = Shopconfig::updateOrCreate(['option'=>'social_media'],['value'=>json_encode($request->except('option'))]);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display footer box of the site.
     */
    public function showFooterBox()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('footer_box')->value ?? null]);
    }

    /**
     * Create or update footer box in storage.
     */
    public function insertFooterBox(Request $request)
    {
        $request->validate([
            'express_name'=>['required','string','max:255'],
            'express_image'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],
            'support_every_day'=>['required','string','max:255'],
            'support_every_day_image'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],
            'guarantee'=>['required','string','max:255'],
            'guarantee_image'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],
            'original'=>['required','string','max:255'],
            'original_image'=>['required','regex:/data:image\\/(jpeg|jpeg|png|svg)/i'],

        ]);
        $this->setAuthorize(Shopconfig::getValue('footer_box'));
        $result = Shopconfig::updateOrCreate(['option'=>'footer_box'],['value'=>json_encode($request->except('option'))]);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display footer box of the site.
     */
    public function showSms()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('sms')->value ?? null]);
    }

    /**
     * Create or update footer box in storage.
     */
    public function insertSms(Request $request)
    {
        $request->validate([
            'sms_driver'=>['required','string','max:255'],
            'name_key'=>['required','string','max:255'],
            'sms_phone'=>['required','numeric','digits:12,12'],
            'sms_password'=>['nullable','string','max:255'],

        ]);
        $this->setAuthorize(Shopconfig::getValue('sms'));
        $result = Shopconfig::updateOrCreate(['option'=>'sms'],['value'=>json_encode($request->except('option'))]);
        $environments = ['SMS_DRIVER'=>$request->sms_driver];

        if($request->sms_driver == 'Ippanel'){
            $environments = array_merge($environments, [
                'IPPANEL_API_KEY'=>"{$request->name_key}",
                'SMS_PHONE_NUMBER'=>'+'.$request->sms_phone,
                'SMS_USERNAME'=>'*******',
                'SMS_PASSWORD'=>'*******',
            ]);
        }
        setEnvironmentValue($environments);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display footer box of the site.
     */
    public function showOtherSetting()
    {
        $this->authorize('viewAny',Shopconfig::class);
        return response(['status'=>'success','data'=>Shopconfig::getValue('other_setting')->value ?? null]);
    }

    /**
     * Create or update footer box in storage.
     */
    public function insertOtherSetting(Request $request)
    {
        $request->validate([
            'max_amount'=>['required','numeric'],
            'min_amount'=>['required','numeric'],
            'returned_days'=>['required','numeric'],
            'mail_mailer'=>['nullable','string','max:255'],
            'host'=>['nullable','string','max:255'],
            'port'=>['nullable','numeric'],
            'mail_username'=>['nullable','string','max:255'],
            'mail_password'=>['nullable','string','max:255'],
            'mail_address'=>['nullable','string','max:255'],

        ]);
        $this->setAuthorize(Shopconfig::getValue('other_setting'));
        $result = Shopconfig::updateOrCreate(['option'=>'other_setting'],['value'=>json_encode($request->except('option'))]);
        setEnvironmentValue([
            'MAIL_MAILER'=>$request->mail_mailer,
            'MAIL_HOST'=>$request->host,
            'MAIL_PORT'=>$request->port,
            'MAIL_USERNAME'=>$request->mail_username,
            'MAIL_PASSWORD'=>$request->mail_password,
            'MAIL_FROM_ADDRESS'=>$request->mail_address,
            'RETURNED_DAY'=>$request->returned_days
        ]);
        return $result ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @param $shopConfig
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function setAuthorize($shopConfig): void
    {
        $shopConfig ? $this->authorize('update',$shopConfig) : $this->authorize('create',Shopconfig::class);
    }

}
