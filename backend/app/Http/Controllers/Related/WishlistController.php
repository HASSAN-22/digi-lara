<?php

namespace App\Http\Controllers\Related;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Wishlist::class);
        $wishlists = Wishlist::with(['product'=>fn($q)=>$q->select('id','ir_name','slug','image')])->latest('id')->search()->paginate(10);
        return response(['status'=>'success','data'=>$wishlists]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        $this->authorize('delete',$wishlist);
        return $wishlist->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }
}
