<?php


namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Menentukan apakah user bisa melihat list menu "Users" di Filament sidebar
     */
    public function viewAny(User $user): bool
    {
        // 🔒 Hanya Admin Marketing (SP) yang bisa melihat menu manajemen User/Affiliate ini
        return $user->role === 'admin';
    }

    

    // Untuk fungsi lain seperti view, create, update, delete bisa kamu return true atau sesuaikan kebutuhan
    public function view(User $user, User $model): bool { return true; }
    public function create(User $user): bool { return $user->role === 'admin'; }
    public function update(User $user, User $model): bool { return $user->role === 'admin'; }
    public function delete(User $user, User $model): bool { return $user->role === 'admin'; }
}

// namespace App\Policies;

// use App\Models\User;
// use Illuminate\Auth\Access\Response;

// class UserPolicy
// {
//     /**
//      * Determine whether the user can view any models.
//      */
//     public function viewAny(User $user): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can view the model.
//      */
//     public function view(User $user, User $model): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can create models.
//      */
//     public function create(User $user): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can update the model.
//      */
//     public function update(User $user, User $model): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can delete the model.
//      */
//     public function delete(User $user, User $model): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can restore the model.
//      */
//     public function restore(User $user, User $model): bool
//     {
//         return false;
//     }

//     /**
//      * Determine whether the user can permanently delete the model.
//      */
//     public function forceDelete(User $user, User $model): bool
//     {
//         return false;
//     }
// }
