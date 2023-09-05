<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Product::class);

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Product::class);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Product::class);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Product::class);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, Product::class);

    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnySeller(User $user): bool
    {
        return $user->isSeller();

    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewSeller(User $user, Product $product): bool
    {
        return $user->isSeller() and $product->user_id == $user->id;

    }

    /**
     * Determine whether the user can create models.
     */
    public function createSeller(User $user): bool
    {
        return $user->isSeller();

    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateSeller(User $user, Product $product): bool
    {
        return $user->isSeller() and $product->user_id == $user->id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteSeller(User $user, Product $product): bool
    {
        return $user->isSeller() and $product->user_id == $user->id;

    }

    /////////////////// Admin amazing product /////////////////
    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyAmazingProduct(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, 'view_any_amazing_products');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewAmazingProduct(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, 'view_amazing_products');
    }

    /**
     * Determine whether the user can create models.
     */
    public function createAmazingProduct(User $user): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, 'create_amazing_products');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateAmazingProduct(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, 'update_amazing_products');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAmazingProduct(User $user, Product $product): bool
    {
        return $user->isAdmin() and PermissionService::hasPermission($user, __FUNCTION__, 'delete_amazing_products');
    }

    /////////////////// Seller amazing product /////////////////
    /**
     * Determine whether the user can view any models.
     */
    public function viewAnySellerAmazingProduct(User $user): bool
    {
        return $user->isSeller();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteSellerAmazingProduct(User $user, Product $product): bool
    {
        return $user->isSeller() and $product->user_id == $user->id;
    }


    ////////////////////////////////// Import product ///////////////////////////////////////

    public function importProduct(User $user){
        return $user->isSeller() or $user->isAdmin();
    }
}
