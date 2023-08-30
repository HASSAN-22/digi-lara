<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function productReport(){
        $this->authorize('viewAny',Report::class);
        $reports = Report::where('reportable_type','App\Models\Product')->with([
            'reportable'=>fn($q)=>$q->select('id','ir_name','created_at'),
            'user'=>fn($q)=>$q->select('id','mobile','name')->with('profile')])->latest()->search('App\Models\Product')->paginate(10);
        return response(['status'=>'success','data'=>$reports]);
    }

    public function commentReports(){
        $this->authorize('viewAny',Report::class);
        $reports = Report::where('reportable_type','App\Models\Productcomment')->with([
            'reportable'=>fn($q)=>$q->with(['product'=>fn($q)=>$q->select('id','ir_name','created_at')]),
            'user'=>fn($q)=>$q->select('id','mobile','name')->with('profile')])->latest()->search('App\Models\Productcomment')->paginate(10);
        return response(['status'=>'success','data'=>$reports]);
    }
}
