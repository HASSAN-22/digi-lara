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
        $user = auth()->user();
        $wallet = Wallet::firstOrNew(['user_id'=>$user->id]);
        $wallet->amount = $wallet->amount ?? 0;
        $wallet->save();
        try {
            $redirectUrl = Payment::driver('Zibal')->request(setGateway($request->amount, $wallet->id, $user->mobile, 'wallet'));
        }catch (\Exception $e){
            return response(['status'=>'error'],500);
        }
        return response(['status'=>'success','data'=>['redirect_url'=>$redirectUrl]]);
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
