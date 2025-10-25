<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomFeatures;
use App\Models\PropertyRooms;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class PropertyRoomFeaturesController extends Controller
{
    private $screen = 'property_room_features';

    public function getPropertyRoomFeatures(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomFeatureId = !empty($request->property_room_feature_id) ? $request->property_room_feature_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = PropertyRoomFeatures::select(
            'property_room_features.*',

        )
            ->join('property_rooms', 'property_room_features.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_features.feature', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomFeatureId), function ($query) use ($propertyRoomFeatureId) {
                return $query->where('property_room_features.uuid', $propertyRoomFeatureId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->where('property_room_features.status', $status)
            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getPropertyRoomFeature(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomFeatureId = !empty($request->property_room_feature_id) ? $request->property_room_feature_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = PropertyRoomFeatures::select(
            'property_room_features.*',

        )
            ->join('property_rooms', 'property_room_features.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_features.feature', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomFeatureId), function ($query) use ($propertyRoomFeatureId) {
                return $query->where('property_room_features.uuid', $propertyRoomFeatureId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->where('property_room_features.status', $status)
            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->first();


        return response()->json($out);

    }

    public function setPropertyRoomFeature(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'property_room_id' => ['required'],
            'feature' => ['required', 'max:500'],
        ]);

        if (!empty($request->property_room_feature_id)){
            $save = PropertyRoomFeatures::where('uuid', $request->property_room_feature_id)->first();
        }else{

            $propertyRoom = PropertyRooms::where('uuid', $request->property_room_id)->first();

            $save = new PropertyRoomFeatures();
            $save->property_room_id = $propertyRoom->id;
            $save->status = 1;
        }

        $save->feature = !empty($request->feature) ? $request->feature : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = PropertyRoomFeatures::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getPropertyRoomFeature = PropertyRoomFeatures::find($save->id);

        return response()->json($getPropertyRoomFeature);
    }
}
