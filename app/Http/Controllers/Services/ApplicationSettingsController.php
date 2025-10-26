<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ApplicationSettings;
use App\Models\PropertyRoomEquipments;
use App\Models\PropertyRooms;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ApplicationSettingsController extends Controller
{
    private $screen = 'application_settings';

    public function getApplicationSetting(Request $request){
        $out = ApplicationSettings::find(1);
        return response()->json($out);
    }

    public function setApplicationSetting(Request $request){
        $out = [];

        $save = ApplicationSettings::find(1);

        $save->is_vat = !empty($request->is_vat) ? $request->is_vat : 0;
        $save->vat = !empty($request->vat) ? $request->vat : null;
        $save->is_commission = !empty($request->is_commission) ? $request->is_commission : 0;
        $save->commission = !empty($request->commission) ? $request->commission : null;
        $save->is_genearal_disscount = !empty($request->is_genearal_disscount) ? $request->is_genearal_disscount : 0;
        $save->genearal_disscount = !empty($request->genearal_disscount) ? $request->genearal_disscount : null;

        $save->save();

        return response()->json($save);
    }
}
