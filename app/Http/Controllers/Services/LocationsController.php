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
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = Locations::select(
            'locations.*',
            'districts.district',
            'provinces.province',
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
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('locations.status', $status);
            })
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->orderBy('id', 'ASC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

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
            ->where('districts.status', $status)
            ->where('provinces.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setLocation(Request $request){
        $out = [];


        if (!empty($request->location_id)){
            $validated = $request->validate([
                'district_id' => 'required',
                'location_id' => 'required',
                'location' => 'required|unique:locations,location,' . $request->location_id . ',uuid',
            ]);
            $save = Locations::where('uuid', $request->location_id)->first();
        }else{

            $validated = $request->validate([
                'district_id' => 'required',
                'location' => 'required|unique:locations',
            ]);

            $save = new Locations();
            $save->status = 1;
        }

        $save->district_id = $request->district_id;
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

        $out = [
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Location Has Been updated!',
        ];

        return response()->json($out);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = Locations::where('uuid', $request->id)->first();
        $updatedStatus = 1;

        if (!empty($save->status)){
            $updatedStatus = 0;
        }
        $save->status = $updatedStatus;
        $save->save();


        $out = [
            'updated_status' => $updatedStatus,
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Status Has Been Changed!',
        ];

        return response()->json($out);
    }
}
