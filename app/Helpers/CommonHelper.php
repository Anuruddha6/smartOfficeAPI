<?php

namespace App\Helpers;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonHelper
{

    public function generateUUId($res = [])
    {
        if (!empty($res)){
            $screen = !empty($res['screen']) ? $res['screen'] : 'temp';
            $id = !empty($res['id']) ? $res['id'] : rand(0,99999999);
            $uuId = sha1( $screen . $id . time());
        }
        else{
            $uuId = sha1(rand(0,99999999) . rand(0,99999999) . rand(0,99999999) );
        }

        return $uuId;
    }
}
