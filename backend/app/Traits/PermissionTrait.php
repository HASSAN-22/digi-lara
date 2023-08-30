<?php

namespace App\Traits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;

trait PermissionTrait
{

    public function indexPermission(){
        $this->authorize('viewAny',PermissionModel::class);
        $search = trim(request()->get('search'));
        $query = new RoleModel();
        if($search != ''){
            $query = $query->where('name','like',"%$search%");
        }
        $results = $query->has('permissions')->latest()->with('permissions')->paginate(10);
        return response(['status'=>'success','data'=>$results]);
    }

    public function createPermission(Request $request){
        $request->validate($this->validation());
        $this->authorize('create',RoleModel::class);
        $role = RoleModel::find($request->role_name);
        try{
            $role->givePermissionTo($request->permissions);
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            return response(['status'=>'error'],500);
        }
    }

    public function showPermission(RoleModel $role){
        $this->authorize('view',$role);
        $roles = $role->where('id',$role->id)->with('permissions')->first();
        return response(['status'=>'success', 'data'=>$roles]);
    }

    public function updatePermission(Request $request){
        $request->validate($this->validation());
        $role = RoleModel::find($request->role_name);
        $this->authorize('update',$role);
        DB::beginTransaction();
        try{
            $role->syncPermissions($request->permissions);
            User::whereHas('roles',fn($q)=>$q->where('name',$role->name))->get()->map(fn($user)=>$user->syncPermissions($request->permissions));
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }

    /**
     * @return array
     */
    public function validation(): array
    {
        return [
            'role_name'=>['required','numeric','exists:roles,id'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'string', 'max:255', 'exists:permissions,name'],
        ];
    }

}
