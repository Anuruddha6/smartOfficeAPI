<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Helpers\DBHelper;
use App\Validator\APIValidator;

class PropertiesController extends Controller
{
    private $screen = 'properties';

    public function getProperties(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;
        $isVerified = !empty($request->is_verified) ? $request->is_verified : 1;

        $out = User::select(
            'properties.*',
            'locations.location',
        )
            ->join('locations', 'properties.location_id', 'locations.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('properties.name', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_1', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.address_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.town', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.city', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('properties.location_id', $locationId);
            })
            ->when(!empty($isDeleted), function ($query) use ($isDeleted) {
                return $query->where('properties.is_deleted', 1);
            }, function ($query) use ($request){
                return $query->where('properties.is_deleted', 0);
            })
            ->where('properties.status', $status)
            ->where('properties.is_verified', $isVerified)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getProperty(Request $request){

    }

    public function setProperty(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'location_id' => ['required'],
            'name' => ['required', 'max:500'],
        ]);


        if (!empty($request->property_id)){
            $save = Properties::where('uuid', $request->property_id)->first();
        }else{

            $save = new Properties();
            $save->is_verified = 0;
            $save->is_deleted = 0;
            $save->status = 1;
        }


        $save->location_id = !empty($request->location_id) ? $request->location_id : 0;
        $save->name = !empty($request->name) ? $request->name : null;
        $save->phone_1 = !empty($request->phone_1) ? $request->phone_1 : null;
        $save->phone_2 = !empty($request->phone_2) ? $request->phone_2 : null;
        $save->street_address = !empty($request->street_address) ? $request->street_address : null;
        $save->address_2 = !empty($request->address_2) ? $request->address_2 : null;
        $save->town = !empty($request->town) ? $request->town : null;
        $save->city = !empty($request->city) ? $request->city : null;

        $save->save();


        if (empty($save->uuid) && empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Properties::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getProperty = Properties::find($save->id);

        return response()->json($getProperty);
    }
}
