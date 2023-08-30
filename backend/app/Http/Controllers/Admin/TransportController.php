<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WeightTypeEnum;
use App\Enums\YesOrNoEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransportRequest;
use App\Http\Resources\TransportResource;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Transport::class);
        $transports = Transport::latest()->search()->paginate(10);
        return TransportResource::collection($transports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransportRequest $request)
    {
        $this->authorize('create',Transport::class);
        $transport = Transport::create([
            'name'=>$request->transport_name,
            'fixed_price'=>$request->fixed_price,
            'is_free'=>!empty($request->is_free) ? YesOrNoEnum::from($request->is_free) : null,
            'is_freight'=>!empty($request->is_freight) ? YesOrNoEnum::from($request->is_freight) : null,
            'tax'=>$request->tax,
            'from_day'=>$request->from_day,
            'to_day'=>$request->to_day,
            'weight_type'=>WeightTypeEnum::from($request->weight_type),
        ]);
        return $transport ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transport $transport)
    {
        $this->authorize('view',$transport);
        return response(['status'=>'success','data'=>$transport]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransportRequest $request, Transport $transport)
    {
        $this->authorize('update',$transport);
        $transport = $transport->update([
            'name'=>$request->transport_name,
            'fixed_price'=>$request->fixed_price,
            'is_free'=>!empty($request->is_free) ? YesOrNoEnum::from($request->is_free) : null,
            'is_freight'=>!empty($request->is_freight) ? YesOrNoEnum::from($request->is_freight) : null,
            'tax'=>$request->tax,
            'from_day'=>$request->from_day,
            'to_day'=>$request->to_day,
            'weight_type'=>WeightTypeEnum::from($request->weight_type),
        ]);
        return $transport ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transport $transport)
    {
        $this->authorize('delete',$transport);
        return $transport->delete() ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }
}
