<?php

namespace App\Helpers;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonHelper
{

    public function getAppUrl($params = null)
    {
        $url = env('APP_URL');
        if (!empty($params)) {
            $url = str_ends_with($url, '/') ? $url : $url . '/';
            $params = str_starts_with($params, '/') ? substr($params, 1) : $params;

            $url .= $params;
        }
        return $url;

    }

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
