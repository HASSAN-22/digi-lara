<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{

    private $models;


    /**
     * Run the database seeds.
     */
    public function run()
    {
//        User::create([
//            'access'=>'admin',
//            'name'=>'armia',
//            'mobile'=>'09168963472',
//            'email'=>'armiaevil@gmail.com',
//            'password'=>bcrypt('12345678'),
//            'province_id'=>1,
//            'city_id'=>1,
//            'status'=>'yes',
//        ]);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions= array_merge(
//            $this->permission('users'),
//            $this->permission('categories'),
//            $this->permission('properties'),
//            $this->permission('brands'),
//            $this->permission('guarantees'),
//            $this->permission('products'),
//            $this->permission('widgets'),
//            $this->permission('sliders'),
//            $this->permission('amazing_products'),
//            $this->permission('sizes'),
//            $this->permission('colors'),
//            $this->permission('coupons'),
//            $this->permission('works'),
//            $this->permission('orders'),
//            $this->permission('transports'),
//            $this->permission('becomesellers'),
//            $this->permission('faqs'),
//            $this->permission('news'),
//            $this->permission('pages'),
//            $this->permission('ticketcategories'),
//            $this->permission('tickets'),
//            $this->permission('contacts'),
//            $this->permission('withdrawwallets'),
//            $this->permission('newsletters'),
//            $this->permission('productcomments'),
//            $this->permission('productquestions'),
//            $this->permission('productquestionanswers'),
//            $this->permission('emails'),
//            $this->permission('sms'),
//            $this->permission('returneds'),
            $this->permission('debtors'),
        );
        Permission::insert($permissions);

//        $user = User::first();
//        $role = Role::first();
//        $user->syncRoles([$role->name]);
//        $role->syncPermissions($permissions);
//        foreach ($this->models as $model){
//            $user->givePermissionTo([
//                'view_any_'.$model,
//                'view_'.$model,
//                'create_'.$model,
//                'update_'.$model,
//                'delete_'.$model
//            ]);
//        }

    }

    private function permission($name){
        $this->models[] = $name;
        return [
            ['name'=>'view_any_'.$name,'guard_name'=>'sanctum','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'view_'.$name,'guard_name'=>'sanctum','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'create_'.$name,'guard_name'=>'sanctum','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'update_'.$name,'guard_name'=>'sanctum','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'delete_'.$name,'guard_name'=>'sanctum','created_at'=>now(),'updated_at'=>now()],
        ];
    }
}
