<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role as RoleModel;
trait RoleTrait
{
    public function indexRole(){
        $this->authorize('viewAny',RoleModel::class);
        $search = trim(request()->get('search'));
        $query = new RoleModel();
        if($search != ''){
            $query = $query->where('name','like',"%$search%");
        }
        $roles = $query->latest()->paginate(10);
        return response(['status'=>'success','data'=>$roles]);
    }

    public function storeRole(Request $request){
        $this->authorize('create',RoleModel::class);
        $request->validate(['role_name'=>['required','string','max:255','unique:roles,id']]);
        $role = RoleModel::create(['name'=>$request->role_name]);
        return $role ? response(['status'=>'success'], 201) : response(['status'=>'error'],500);
    }

    public function showRole(RoleModel $role){
        $this->authorize('view',$role);
        return response(['status'=>'success','data'=>$role]);
    }

    public function updateRole(Request $request, RoleModel $role){
        $this->authorize('update',$role);
        $request->validate(['role_name'=>['required','string','max:255','unique:roles,id,name']]);
        $role = $role->update(['name'=>$request->role_name]);
        return $role ? response(['status'=>'success'], 201) : response(['status'=>'error'],500);
    }
}
