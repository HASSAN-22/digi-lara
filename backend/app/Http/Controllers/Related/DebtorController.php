<?php

namespace App\Http\Controllers\Related;

use App\Enums\DebtorStatusEnum;
use App\Enums\PaidByEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\DebtorResource;
use App\Models\Debtor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;

class DebtorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Debtor::class);
        $debtors = Debtor::latest()->with([
            'user'=>fn($q)=>$q->select('id','name')->with(['becomeSellers'=>fn($q)=>$q->where('status',StatusEnum::ACTIVE)])
        ])->search()->paginate(10);
        return DebtorResource::collection($debtors);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function pay(Request $request, Debtor $debtor)
    {
        $this->authorize('pay',$debtor);
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if($request->pay_type == 'wallet'){
                $wallet = $user->wallet;
                if($wallet->amount > $debtor->amount){
                    $debtor->transactions()->create([
                        'user_id'=>$user->id,
                        'amount'=>$debtor->amount,
                        'ref_id'=> \Illuminate\Support\Str::random(8),
                        'status'=>StatusEnum::ACTIVE,
                        'paid_by'=>PaidByEnum::USER,
                    ]);
                    $debtor->update(['amount'=>0,'status'=>DebtorStatusEnum::SETTLED]);
                    DB::commit();
                    return response(['status'=>'success'],201);
                }
                return response(['status'=>'success','msg'=>'موجودی کیف پول کافی نیست'],500);
            }else{
                // connect to bank
            }
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

}
