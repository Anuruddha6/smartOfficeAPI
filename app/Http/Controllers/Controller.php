<?php

namespace App\Http\Controllers;

use App\Helpers\UsersHelper;
use Illuminate\Http\Request;

abstract class Controller
{
    public $defaultItemsPerPage = 20;

    public $userId = 0;
    public $userUUId = 0;
    public $isSuperAdmin = 0;
    public $isAdmin = 0;
    public $isVendor = 0;
    public $isUser = 0;

    public function __construct(Request $request){
        if (!empty($request->bearerToken())){
            $token = $request->bearerToken();

            $u = new UsersHelper();
            $user = $u->getUserByPublicKey($token);

            if (!empty($user)){
                $this->userId = $user->id;
                $this->userUUId = $user->uuid;

                if ($user->user_role_id == 1){
                    $this->isSuperAdmin = 1;
                }elseif ($user->user_role_id == 2){
                    $this->isAdmin = 1;
                }elseif ($user->user_role_id == 3){
                    $this->isVendor = 1;
                }elseif ($user->user_role_id == 4){
                    $this->isUser = 1;
                }
            }


        }
    }


    public function dbInsertTime($dateTime = null){
        if (empty($dateTime)){
            $now = date('Y-m-d H:i:s', strtotime($dateTime));
        }else{
            $now = date('Y-m-d H:i:s', time());
        }
        return $now;
    }

}
