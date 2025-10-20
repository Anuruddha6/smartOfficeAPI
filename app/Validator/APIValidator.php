<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIValidator
{
    public static function validate(Request $res, $rules)
    {
        $validator = Validator::make($res->all(), $rules);
        if ($validator->fails()) {
            response()->json($validator->errors(), 400)->throwResponse();
        }
    }

    public static function validateArray($res, $rules)
    {
        $validator = Validator::make($res, $rules);
        if ($validator->fails()) {
            response()->json($validator->errors(), 400)->throwResponse();
        }
    }
}
