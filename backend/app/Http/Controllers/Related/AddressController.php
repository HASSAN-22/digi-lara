<?php

namespace App\Http\Controllers\Related;

use App\Enums\IsSelectedAddressEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Address::class);
        $address = Address::latest()->with(['province','city'])->paginate(10);
        $provinces = Province::get();
        return AddressResource::collection($address)->additional(['provinces'=>$provinces]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressRequest $request)
    {
        $this->authorize('create',Address::class);
        $address = Address::create([
            'user_id'=>auth()->Id(),
            'province_id'=>$request->province_id,
            'city_id'=>$request->city_id,
            'address'=>$request->address,
            'plaque'=>$request->plaque,
            'unit'=>$request->unit,
            'postal_code'=>$request->postal_code,
            'receiver_name'=>$request->receiver_name,
            'receiver_last_name'=>$request->receiver_last_name,
            'mobile'=>$request->mobile,
            'is_selected'=>$request->exists('is_selected') ? IsSelectedAddressEnum::from($request->is_selected) : 'no',
        ]);
        return $address ? response(['status'=>'success'], 201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        $this->authorize('view',$address);
        return response(['status'=>'success','data'=>$address]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressRequest $request, Address $address)
    {
        $this->authorize('update',$address);

        $address = $address->update([
            'user_id'=>auth()->Id(),
            'province_id'=>$request->province_id,
            'city_id'=>$request->city_id,
            'address'=>$request->address,
            'plaque'=>$request->plaque,
            'unit'=>$request->unit,
            'postal_code'=>$request->postal_code,
            'receiver_name'=>$request->receiver_name,
            'receiver_last_name'=>$request->receiver_last_name,
            'mobile'=>$request->mobile,
        ]);
        return $address ? response(['status'=>'success'], 201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete',$address);
        return $address->delete() ? response(['status'=>'success'], 201) : response(['status'=>'error'],500);
    }
}
