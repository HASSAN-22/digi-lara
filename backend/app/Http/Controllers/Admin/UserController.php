<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    private string $DIRECTORY = 'uploader/avatar';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',User::class);
        $users = User::latest()->search()->with('roles')->paginate(10);
        return UserResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create',User::class);
        DB::beginTransaction();
        try{
            $user = User::create([
                'name'=>$request->name,
                'access'=>UserAccessEnum::from($request->access),
                'mobile'=>$request->mobile,
                'password'=>Hash::make($request->password),
                'status'=>StatusEnum::from($request->status),
                'avatar'=>Upload::upload($request,'avatar',$this->DIRECTORY),
            ]);
            if(!empty($request->role)){
                $this->syncRoleAndPermissions($request->role, $user);
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view',$user);
        $user = $user->where('id',$user->id)->with('roles')->first();
        return response(['status'=>'success','data'=>$user]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update',$user);
        DB::beginTransaction();
        try{
            $pervAvatar = $user->avatar;
            $result = $user->update([
                'name'=>$request->name,
                'access'=>$request->access,
                'mobile'=>$request->mobile,
                'password'=>!empty($request->password) ? Hash::make($request->password) : $user->password,
                'status'=>StatusEnum::from($request->status),
                'avatar'=>Upload::upload($request,'avatar',$this->DIRECTORY,$user->avatar),
            ]);
            if($result){
                if($request->hasFile('avatar')){
                    removeFile($pervAvatar);
                }
            }
            if(!empty($request->role)){
                $this->syncRoleAndPermissions($request->role, $user);
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete',$user);
        if($user->delete()){
            removeFile($user->avatar);
            return response(['status'=>'success']);
        }
        return response(['status'=>'error'],500);
    }

    /**
     * @param string|int $role
     * @param $user
     * @return void
     */
    public function syncRoleAndPermissions(string|int $role, $user): void
    {
        $role = Role::find($role);
        $user->syncRoles([$role->name]);
        $user->syncPermissions($role->permissions()->pluck('name'));
    }
}
