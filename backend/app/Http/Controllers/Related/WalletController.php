<?php

namespace App\Http\Controllers\Related;

use App\Enums\PaidByEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Bank\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Wallet::class);
        $user = auth()->user();
        $query = $user->isAdmin() ? (new Transaction())->where('transactionable_type','App\Models\Wallet') : $user->transactions('Wallet');
        $transaction = $query->search()->with(['user'=>fn($q)=>$q->select('id','name')])->paginate(10);
        return WalletResource::collection($transaction);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(WalletRequest $request)
    {
        $this->authorize('create',Wallet::class);
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $wallet = Wallet::updateOrcreate(['user_id'=>$user->id],['amount'=>DB::raw("amount + {$request->amount}")]);
            $wallet->transactions()->create([
               'user_id'=>$user->id,
               'amount'=>$request->amount,
               'ref_id'=>Str::random(8),
               'status'=>StatusEnum::ACTIVE,
               'paid_by'=>PaidByEnum::USER,
            ]);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    //c3d69a91-a58f-46de-9f1e-2dd32e8ac8e2
//        $data = [
//            'merchantId' 		=> 'c3d69a91-a58f-46de-9f1e-2dd32e8ac8e2',
//            'amount' 			=> $request->amount,
//            'callback' 		=> route('transaction.verify',['type'=>'wallet']),
//        ];
//
//        try{
//            Payment::driver(config('app.payment_driver'))->request($data);
//        }catch (\Exception $e){
//            return response(['status'=>'error','msg'=>$e->getMessage()],500);
//        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
