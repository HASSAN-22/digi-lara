<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\Propertytype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Property::class);
        $properties = Property::search()->latest()->paginate(10);
        return response(['status'=>'success','data'=>$properties]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyRequest $request)
    {
        $this->authorize('create',Property::class);
        DB::beginTransaction();
        try{
            $property = Property::create([
                'property'=>$request->property
            ]);
            $propertyTypes = [];
            foreach($request->property__types as $property_type){
                $propertyTypes[] = [
                    'property_id'=>$property->id,
                    'name'=>$property_type
                ];
            }
            Propertytype::insert($propertyTypes);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return  response(['status'=>'error'],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $this->authorize('view',$property);
        $property = $property->where('id',$property->id)->with('propertyTypes')->first();
        return response(['status'=>'success','data'=>$property],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropertyRequest $request, Property $property)
    {
        $this->authorize('update',$property);
        DB::beginTransaction();
        try{
            $property->update([
                'property'=>$request->property
            ]);
            $property->propertyTypes()->delete();
            $propertyTypes = [];
            foreach($request->property__types as $property_type){
                $propertyTypes[] = [
                    'property_id'=>$property->id,
                    'name'=>$property_type
                ];
            }
            Propertytype::insert($propertyTypes);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return  response(['status'=>'error'],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $this->authorize('delete',$property);
        return $property->delete() ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }
}
