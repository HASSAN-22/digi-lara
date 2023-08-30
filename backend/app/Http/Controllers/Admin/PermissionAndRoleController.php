<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\PermissionTrait;
use App\Traits\RoleTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role as RoleModel;

class PermissionAndRoleController extends Controller
{
    use RoleTrait, PermissionTrait;


    public function destroy(RoleModel $role){
        $this->authorize('delete',$role);
        return $role->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    public function getRoles(){
        $this->authorize('viewAny',RoleModel::class);
        $roles = RoleModel::get();
        return response(['status'=>'success','data'=>$roles]);
    }
}
