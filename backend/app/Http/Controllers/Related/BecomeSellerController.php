<?php

namespace App\Http\Controllers\Related;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeSellerRequest;
use App\Http\Resources\BecomesellerResource;
use App\Models\Becomeseller;
use App\Models\Becomesellerlegal;
use App\Models\Becomesellerreal;
use App\Models\Province;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class BecomeSellerController extends Controller
{
    private string $DIRECTORY = 'uploader/becomeseller';

    /**
     * Get all data
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'viewAnyAdmin' : 'viewAny',Becomeseller::class);

        $notifications = NotificationService::name('BecomesellerNotification')->forUser((int)$user->id)->getUnRead();
        $query = new Becomeseller();
        if(!$user->isAdmin()){
            $query = $query->where('user_id',$user->id);
        }
        $becomeSellers = $query->latest()->search()->paginate(10);
        $provinces = Province::get();
        return BecomesellerResource::collection($becomeSellers)->additional(['provinces'=>$provinces,'notifications'=>$notifications]);
    }

    /**
     * Insert data
     * @param BecomeSellerRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(BecomeSellerRequest $request){
        $this->authorize('create',Becomeseller::class);
        DB::beginTransaction();
        try{
            $becomeSeller = Becomeseller::create([
                'user_id'=>auth()->Id(),
                'province_id'=>$request->province_id,
                'city_id'=>$request->city_id,
                'shop_name'=>$request->shop_name,
                'type'=>$request->seller_type,
                'postal_code'=>$request->postal_code,
                'phone'=>$request->phone,
                'mobile'=>$request->mobile,
                'shaba'=>$request->shaba,
                'identity_card_front'=>Upload::upload($request,'identity_card_front',$this->DIRECTORY),
                'identity_card_back'=>Upload::upload($request,'identity_card_back',$this->DIRECTORY),
                'status'=>StatusEnum::PENDING,
                'address'=>$request->address,
            ]);

            if($request->seller_type == 'legal'){
                Becomesellerlegal::create([
                    'becomeseller_id'=>$becomeSeller->id,
                    'company_name'=>$request->company_name,
                    'company_type'=>$request->company_type,
                    'registration_number'=>$request->registration_number,
                    'national_identity_number'=>$request->national_identity_number,
                    'economic_number'=>$request->economic_number,
                    'authorized_representative'=>$request->authorized_representative,
                ]);
            }else{
                Becomesellerreal::create([
                    'becomeseller_id'=>$becomeSeller->id,
                    'name'=>$request->name,
                    'last_name'=>$request->last_name,
                    'birth_day'=>$request->birth_day,
                    'gender'=>$request->gender,
                    'identity_card_number'=>$request->identity_card_number,
                    'national_identity_number'=>$request->national_identity_number,
                ]);
            }
            NotificationService::name('BecomesellerNotification')->send((int)$becomeSeller->id,null,true, false);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }

    /**
     * Get becomeseller data with relations
     * @param Becomeseller $becomeseller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Becomeseller $becomeseller){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'viewAdmin' : 'view',$becomeseller);
        $becomeseller = $becomeseller->where('id',$becomeseller->id)->with(['becomeSellerLegal','becomeSellerReal','province','city','user'=>fn($q)=>$q->select('id','name')])->first();
        if($becomeseller['becomeSellerLegal']){
            $becomeseller['becomeSellerLegal']['company_type'] = typeService($becomeseller['becomeSellerLegal']['company_type'])->companyType('fa')->get();
        }else if($becomeseller['becomeSellerReal']){
            $becomeseller['becomeSellerReal']['gender'] = typeService($becomeseller['becomeSellerReal']['gender'])->gender('fa')->get();
            $becomeseller['becomeSellerReal']['birth_day'] = dateToPersian($becomeseller['becomeSellerReal']['birth_day']);
        }
        $becomeseller['type'] = typeService($becomeseller['type'])->sellerType('fa')->get();
        $becomeseller['status'] = typeService($becomeseller['status'])->status('fa')->get();
        NotificationService::name('BecomesellerNotification')->forUser((int)$user->id, $user->isAdmin())->read($becomeseller->id,'data->become_seller->id');
        return response(['status'=>'success','data'=>$becomeseller]);
    }

    /**
     * Update status
     * @param Request $request
     * @param Becomeseller $becomeseller
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Becomeseller $becomeseller){
        $this->authorize('updateAdmin',$becomeseller);
        $request->validate([
            'status'=>['required','string',new Enum(StatusEnum::class),'max:255'],
            'reject_reason'=>['required_if:status,==,no','nullable','string','max:5000']
        ]);
        DB::beginTransaction();
        try{
            $becomeseller->update(['status'=>$request->status,'reject_reason'=>$request->reject_reason]);
            $becomeseller->user()->update(['access'=>$request->status == 'yes' ? 'seller' : 'user']);
            NotificationService::name('BecomesellerNotification')->forUser((int)auth()->Id(),true)->read($becomeseller->id,'data->become_seller->id');
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }
}
