<?php

namespace App\Policies;

use Auth;
use App\User;
use App\Role;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of the products.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function index(User $user)
    {
        return ($user->role->id === Role::ROLE_VENDOR || $user->role->id === Role::ROLE_STAFF);
    }

    /**
     * Determine whether the user can view the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function view(User $user, Product $product)
    {
        if ($user->role->id == Role::ROLE_USER) {
            return true;
        }

        $owner = ($user->role->id === Role::ROLE_VENDOR) ? $user : $user->owner;
        return  ($user->role->id === Role::ROLE_VENDOR || $user->role->id === Role::ROLE_STAFF)
            && ( $owner->id == $product->user_id);
    }

    /**
     * Determine whether the user can create products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role->id === Role::ROLE_VENDOR;
    }


    /**
     * Determine whether the user can update the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        $owner = ($user->role->id === Role::ROLE_STAFF) ? $user->owner : $user;
        return  ($user->role->id === Role::ROLE_VENDOR || $user->role->id === Role::ROLE_STAFF)
            && ( $owner->id == $product->user_id);
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        return $user->role->id === Role::ROLE_VENDOR;
    }

    /**
     * Determine whether the user can restore the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function restore(User $user, Product $product)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product.
     *
     * @param  \App\User  $user
     * @param  \App\Product  $product
     * @return mixed
     */
    public function forceDelete(User $user, Product $product)
    {
        //
    }
}
