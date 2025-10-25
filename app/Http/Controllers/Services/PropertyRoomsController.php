<?php

namespace App\Http\Controllers\services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertyRooms;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class PropertyRoomsController extends Controller
{
    private $screen = 'property_rooms';

    public function getPropertyRooms(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $out = PropertyRooms::select(
            'property_rooms.*',

        )
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->with([
                'property_room_equipments' => function ($query) {
                    $query->select(
                        'property_room_equipments.*',
                    )->where('property_room_equipments.status', 1);
                }
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.people', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_hour', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_half_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_halfday', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_hourly', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })

            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getPropertyRoom(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $out = PropertyRooms::select(
            'property_rooms.*',

        )
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->with([
                'property_room_equipments' => function ($query) {
                    $query->select(
                        'property_room_equipments.*',
                    )->where('property_room_equipments.status', 1);
                }
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.people', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_hour', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_half_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_halfday', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_hourly', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setPropertyRoom(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'property_id' => ['required'],
            'name' => ['required', 'max:500'],
        ]);


        if (!empty($request->property_room_id)){
            $save = PropertyRooms::where('uuid', $request->property_room_id)->first();
        }else{
            $property = Properties::where('uuid', $request->property_id)->first();

            $save = new PropertyRooms();
            $save->property_id = $property->id;
            $save->status = 1;
        }

        $save->name = !empty($request->name) ? $request->name : null;
        $save->people = !empty($request->people) ? $request->people : null;
        $save->price_hour = !empty($request->price_hour) ? $request->price_hour : null;
        $save->price_half_day = !empty($request->price_half_day) ? $request->price_half_day : null;
        $save->price_day = !empty($request->price_day) ? $request->price_day : null;
        $save->clearence_period_halfday = !empty($request->clearence_period_halfday) ? $request->clearence_period_halfday : null;
        $save->clearence_period_hourly = !empty($request->clearence_period_hourly) ? $request->clearence_period_hourly : null;
        $save->description = !empty($request->description) ? $request->description : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = PropertyRooms::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getPropertyRoom = PropertyRooms::find($save->id);

        return response()->json($getPropertyRoom);
    }
}
