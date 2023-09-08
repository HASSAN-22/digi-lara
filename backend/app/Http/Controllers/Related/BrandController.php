<?php

namespace App\Http\Controllers\Related;

use App\Enums\BrandTypeEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\User;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private string $DIRECTORY = 'uploader/brand';

    /**
     * Display a listing of the resource.
     */
    public function index(){
        $user = auth()->user();
        $query = Brand::latest()->search()->with(['user'=>fn($q)=>$q->select('id','name')]);
        if($user->isAdmin()){
            $this->authorize('viewAny',Brand::class);
        }else{
            $query = $query->where('user_id',$user->id);
            $this->authorize('viewAnySeller',Brand::class);
        }
        $brands = $query->paginate(10);
        $users = User::where('access','!=','user    ')->get();
        return BrandResource::collection($brands)->additional(['users'=>$users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'create' : 'createSeller',Brand::class);
        $brand = Brand::create([
            'user_id'=>$user->isAdmin() ? $request->user_id : $user->id,
            'name'=>$request->ir_name,
            'en_name'=>$request->en_name,
            'description'=>$request->description,
            'brand_type'=>BrandTypeEnum::from($request->brand_type),
            'registration_form'=>Upload::upload($request,'registration_form',$this->DIRECTORY),
            'link'=>$request->link,
            'logo'=>Upload::upload($request,'logo',$this->DIRECTORY),
            'status'=>$user->isAdmin() ? StatusEnum::from($request->status) :  StatusEnum::PENDING,
        ]);
        return $brand ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand){
        return response(['status'=>'success','data'=>$this->getBrand($brand)]);
    }


    /**
     * Display the specified resource.
     */
    public function showBrand(Brand $brand){
        return new BrandResource($this->getBrand($brand));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request ,Brand $brand){
        $user = auth()->user();

        $this->authorize($user->isAdmin() ? 'update' : 'updateSeller',$brand);
       $brand = $brand->update([
            'user_id'=>$user->isAdmin() ? $request->user_id : $user->id,
            'name'=>$request->ir_name,
            'en_name'=>$request->en_name,
            'description'=>$request->description,
            'brand_type'=>BrandTypeEnum::from($request->brand_type),
            'registration_form'=>Upload::upload($request,'registration_form',$this->DIRECTORY, $brand->registration_form),
            'link'=>$request->link,
            'reason_rejection'=>$request->reason_rejection,
            'logo'=>Upload::upload($request,'logo',$this->DIRECTORY, $brand->logo),
            'status'=>$user->isAdmin() ? StatusEnum::from($request->status) :  StatusEnum::PENDING,
        ]);
        return $brand ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand){
        $this->authorize(auth()->user()->isAdmin() ? 'delete' : 'deleteSeller',$brand);
        if($brand->delete()){
            removeFile($brand->registration_form);
            removeFile($brand->logo);
            return response(['status'=>'success']);
        }
        return response(['status'=>'error'],500);
    }

    /**
     * @param Brand $brand
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function getBrand(Brand $brand)
    {
        $this->authorize(auth()->user()->isAdmin() ? 'view' : 'viewSeller', $brand);
        return $brand->where('id', $brand->id)->with(['user' => fn($q) => $q->select('id', 'name')])->first();
    }

}
