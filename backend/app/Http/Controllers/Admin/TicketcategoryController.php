<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketcategoryRequest;
use App\Models\Ticketcategory;
use Illuminate\Http\Request;

class TicketcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Ticketcategory::class);
        $ticketCategories = Ticketcategory::latest()->paginate(10);
        return response(['status'=>'success','data'=>$ticketCategories]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketcategoryRequest $request)
    {
        $this->authorize('create',Ticketcategory::class);
        $ticketcategory = Ticketcategory::create(['title'=>$request->title]);
        return $ticketcategory ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticketcategory $ticketcategory)
    {
        $this->authorize('view',$ticketcategory);
        return response(['status'=>'success','data'=>$ticketcategory]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TicketcategoryRequest $request, Ticketcategory $ticketcategory)
    {
        $this->authorize('update',$ticketcategory);
        $ticketcategory = $ticketcategory->update(['title'=>$request->title]);
        return $ticketcategory ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticketcategory $ticketcategory)
    {
        $this->authorize('delete',$ticketcategory);
        return $ticketcategory->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);

    }
}
