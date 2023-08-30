<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    /**
     * Check the user have permission
     * @param User $user
     * @param string $currentFunctionName
     * @param mixed $model
     * @return bool
     */
    public static function hasPermission(User $user, string $currentFunctionName, mixed $model){
        if(class_exists($model)){
            $table = (new $model())->getTable();
            $permission = preg_replace('/([A-Z])/', '_$1', $currentFunctionName).'_'.$table;
        }else{
            $permission = preg_replace('/([A-Z])/', '_$1', $model);
        }
        return $user->hasPermissionTo(strtolower($permission));
    }

}
