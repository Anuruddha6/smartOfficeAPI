<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomEquipments;
use App\Models\PropertyRooms;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class PropertyRoomEquipmentsController extends Controller
{
    private $screen = 'property_room_equipments';

    public function getPropertyRoomEquipments(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $PropertyRoomEquipmentId = !empty($request->property_room_equipment_id) ? $request->property_room_equipment_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $out = PropertyRoomEquipments::select(
            'property_room_equipments.*',

        )
            ->join('property_rooms', 'property_room_equipments.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_equipments.equipment', 'like', '%' . $keyword . '%')
                    ->orWhere('property_room_equipments.price', 'like', '%' . $keyword . '%')
                    ->orWhere('property_room_equipments.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($PropertyRoomEquipmentId), function ($query) use ($PropertyRoomEquipmentId) {
                return $query->where('property_room_equipments.uuid', $PropertyRoomEquipmentId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->where('property_room_equipments.status', $status)
            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getPropertyRoomEquipment(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $PropertyRoomEquipmentId = !empty($request->property_room_equipment_id) ? $request->property_room_equipment_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $out = PropertyRoomEquipments::select(
            'property_room_equipments.*',

        )
            ->join('property_rooms', 'property_room_equipments.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_equipments.equipment', 'like', '%' . $keyword . '%')
                    ->orWhere('property_room_equipments.price', 'like', '%' . $keyword . '%')
                    ->orWhere('property_room_equipments.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($PropertyRoomEquipmentId), function ($query) use ($PropertyRoomEquipmentId) {
                return $query->where('property_room_equipments.uuid', $PropertyRoomEquipmentId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->where('property_room_equipments.status', $status)
            ->where('property_rooms.status', $status)
            ->where('properties.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setPropertyRoomEquipment(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'property_room_id' => ['required'],
            'equipment' => ['required', 'max:500'],
        ]);

        if (!empty($request->property_room_equipment_id)){
            $save = PropertyRoomEquipments::where('uuid', $request->property_room_equipment_id)->first();
        }else{

            $propertyRoom = PropertyRooms::where('uuid', $request->property_room_id)->first();

            $save = new PropertyRoomEquipments();
            $save->property_room_id = $propertyRoom->id;
            $save->status = 1;
        }

        $save->equipment = !empty($request->equipment) ? $request->equipment : null;
        $save->price = !empty($request->price) ? $request->price : null;
        $save->description = !empty($request->description) ? $request->description : null;
        $save->is_chargable = !empty($request->is_chargable) ? $request->is_chargable : null;
        $save->quantity = !empty($request->quantity) ? $request->quantity : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = PropertyRoomEquipments::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getPropertyRoomEquipment = PropertyRoomEquipments::find($save->id);

        return response()->json($getPropertyRoomEquipment);
    }
}
