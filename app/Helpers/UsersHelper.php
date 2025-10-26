<?php
namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersHelper
{

    public function getUserByPublicKey($publicKey){
        $out = [];

        if (!empty($publicKey)){
            $out = User::select(
                'users.*',

            )
                ->join('user_roles', 'users.user_role_id', 'users.id')
                ->when(!empty($publicKey), function ($query) use ($publicKey) {
                    return $query->where('users.public_key', $publicKey);
                })
                ->first();
        }

        return $out;

    }

}
