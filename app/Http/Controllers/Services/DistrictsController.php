<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Locations;
use App\Models\Provinces;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class DistrictsController extends Controller
{
    private $screen = 'districts';

    public function getDistricts(Request $request){
        $out = [];

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $provinceId = !empty($request->province_id) ? $request->province_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = Districts::select(
            'districts.*',
        )
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('districts.district', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($districtId), function ($query) use ($districtId) {
                return $query->where('districts.uuid', $districtId);
            })
            ->when(!empty($provinceId), function ($query) use ($provinceId) {
                return $query->where('provinces.uuid', $provinceId);
            })
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->orderBy('id', 'ASC')
            ->get();

        return response()->json($out);
    }

    public function getDistrict(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $provinceId = !empty($request->province_id) ? $request->province_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = Districts::select(
            'districts.*',
        )
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('districts.district', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($districtId), function ($query) use ($districtId) {
                return $query->where('districts.uuid', $districtId);
            })
            ->when(!empty($provinceId), function ($query) use ($provinceId) {
                return $query->where('provinces.uuid', $provinceId);
            })
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->orderBy('id', 'ASC')
            ->first();

        return response()->json($out);

    }
    public function setDistrict(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'province_id' => ['required'],
            'district' => ['required', 'max:500'],
        ]);

        if (!empty($request->district_id)){
            $save = Districts::where('uuid', $request->district_id)->first();
        }else{

            $district = Provinces::where('uuid', $request->province_id)->first();

            $save = new Districts();
            $save->province_id = $district->id;
            $save->status = 1;
        }

        $save->location = !empty($request->location) ? $request->location : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Districts::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getDistricts = Districts::find($save->id);

        return response()->json($getDistricts);
    }
}
