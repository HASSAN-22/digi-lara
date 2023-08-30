<?php

namespace App\Http\Controllers\Related;

use App\Enums\StatusEnum;
use App\Enums\WithdrawStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawwalletResource;
use App\Models\Withdrawwallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class WithdrawwalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Withdrawwallet::class);
        $query = Withdrawwallet::latest()->search();
        $user = auth()->user();
        if(!$user->isAdmin()){
            $query = $query->where('user_id',$user->id);
        }
        $result = $query->with(['user'=>fn($q)=>$q->select('id','name')])->paginate(10);
        return WithdrawwalletResource::collection($result);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',Withdrawwallet::class);
        $request->validate([
            'amount'=>['required','numeric'],
            'shaba'=>['required','numeric','digits:24,24'],
            'cart_number'=>['required','numeric','digits:16,16'],
        ]);
        $user = auth()->user();
        $wallet = $user->wallet;

            if(is_null($wallet) or ((int)$request->amount > (int)$wallet->amount)){
            return response(['status'=>'error','msg'=>'مبلغ درخواستی بیشتر از موجودی کیف پول شماست'],500);
        }
        DB::beginTransaction();
        try{
            $result = Withdrawwallet::create([
                'user_id'=>$user->id,
                'amount'=>$request->amount,
                'shaba'=>$request->shaba,
                'cart_number'=>$request->cart_number,
                'status'=>WithdrawStatusEnum::PENDING,
            ]);
            $user->wallet()->update(['amount'=>DB::raw("amount - {$request->amount}")]);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Withdrawwallet $withdrawwallet)
    {
        $this->authorize('update',$withdrawwallet);
        $request->validate(['status'=>['required','string','max:20',new Enum(WithdrawStatusEnum::class)]]);
        DB::beginTransaction();
        try{
            $result = $withdrawwallet->update(['status'=>WithdrawStatusEnum::from($request->status)]);
            if($request->status === WithdrawStatusEnum::CANCEL->value){
                $withdrawwallet->user->wallet()->update(['amount'=>DB::raw("amount + {$withdrawwallet->amount}")]);
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Withdrawwallet $withdrawwallet)
    {
        //
    }
}
