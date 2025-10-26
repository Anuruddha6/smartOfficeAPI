<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Locations;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    private $screen = 'locations';

    public function getLocations(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = Locations::select(
            'locations.*',

        )
            ->join('districts', 'locations.district_id', 'districts.id')
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('locations.location', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('locations.uuid', $locationId);
            })
            ->when(!empty($districtId), function ($query) use ($districtId) {
                return $query->where('districts.uuid', $districtId);
            })
            ->where('locations.status', $status)
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getLocation(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = Locations::select(
            'locations.*',

        )
            ->join('districts', 'locations.district_id', 'districts.id')
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('locations.location', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('locations.uuid', $locationId);
            })
            ->when(!empty($districtId), function ($query) use ($districtId) {
                return $query->where('districts.uuid', $districtId);
            })
            ->where('locations.status', $status)
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setLocation(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'district_id' => ['required'],
            'location' => ['required', 'max:500'],
        ]);

        if (!empty($request->location_id)){
            $save = Locations::where('uuid', $request->location_id)->first();
        }else{

            $district = Districts::where('uuid', $request->district_id)->first();

            $save = new Locations();
            $save->district_id = $district->id;
            $save->status = 1;
        }

        $save->location = !empty($request->location) ? $request->location : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Locations::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getLocation = Locations::find($save->id);

        return response()->json($getLocation);
    }
}
