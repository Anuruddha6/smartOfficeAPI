<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomEquipments;
use App\Models\PropertyRooms;
use App\Models\ReservationDetailEquipments;
use App\Models\ReservationDetails;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationDetailEquipmentsController extends Controller
{
    private $screen = 'reservation_detail_equipments';

    public function ReservationDetailEquipments(Request $request){


        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationDetailEquipmentId = !empty($request->reservation_detail_equipment_id) ? $request->reservation_detail_equipment_id : 0;
        $reservationDetailId = !empty($request->reservation_detail_id) ? $request->reservation_detail_id : 0;
        $propertyRoomEquipmentId = !empty($request->property_room_equipment_id) ? $request->property_room_equipment_id : 0;
        $status = !empty($request->status) ? $request->status : 1;

        $out = ReservationDetailEquipments::select(
            'reservation_detail_equipments.*',
            'property_room_equipments.equipment',

        )
            ->join('reservation_details', 'reservation_detail_equipments.reservation_detail_id', 'reservation_details.id')
            ->join('property_room_equipments', 'reservation_detail_equipments.property_room_equipment_id', 'property_room_equipments.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_equipments.equipment', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationDetailEquipmentId), function ($query) use ($reservationDetailEquipmentId) {
                return $query->where('reservation_detail_equipments.uuid', $reservationDetailEquipmentId);
            })
            ->when(!empty($reservationDetailId), function ($query) use ($reservationDetailId) {
                return $query->where('reservation_details.uuid', $reservationDetailId);
            })
            ->when(!empty($propertyRoomEquipmentId), function ($query) use ($propertyRoomEquipmentId) {
                return $query->where('property_room_equipments.uuid', $propertyRoomEquipmentId);
            })

            ->where('property_room_equipments.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getReservationDetailEquipment(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationDetailEquipmentId = !empty($request->reservation_detail_equipment_id) ? $request->reservation_detail_equipment_id : 0;
        $reservationDetailId = !empty($request->reservation_detail_id) ? $request->reservation_detail_id : 0;
        $propertyRoomEquipmentId = !empty($request->property_room_equipment_id) ? $request->property_room_equipment_id : 0;
        $status = !empty($request->status) ? $request->status : 1;

        $out = ReservationDetailEquipments::select(
            'reservation_detail_equipments.*',
            'property_room_equipments.equipment',

        )
            ->join('reservation_details', 'reservation_detail_equipments.reservation_detail_id', 'reservation_details.id')
            ->join('property_room_equipments', 'reservation_detail_equipments.property_room_equipment_id', 'property_room_equipments.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_room_equipments.equipment', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationDetailEquipmentId), function ($query) use ($reservationDetailEquipmentId) {
                return $query->where('reservation_detail_equipments.uuid', $reservationDetailEquipmentId);
            })
            ->when(!empty($reservationDetailId), function ($query) use ($reservationDetailId) {
                return $query->where('reservation_details.uuid', $reservationDetailId);
            })
            ->when(!empty($propertyRoomEquipmentId), function ($query) use ($propertyRoomEquipmentId) {
                return $query->where('property_room_equipments.uuid', $propertyRoomEquipmentId);
            })

            ->where('property_room_equipments.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setReservationDetailEquipment(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'reservation_detail_id' => ['required'],
            'property_room_equipment_id' => ['required'],
        ]);

        if (!empty($request->reservation_detail_equipment_id)){
            $save = ReservationDetailEquipments::where('uuid', $request->reservation_detail_equipment_id)->first();
        }else{

            $propertyRoomEquipment = PropertyRoomEquipments::where('uuid', $request->property_room_equipment_id)->first();
            $reservationDetail = ReservationDetails::where('uuid', $request->reservation_detail_id)->first();

            $save = new ReservationDetailEquipments();
            $save->reservation_detail_id = $reservationDetail->id;
            $save->property_room_equipment_id = $propertyRoomEquipment->id;
            $save->status = 1;
        }

        $save->price = !empty($request->price) ? $request->price : null;
        $save->quantity = !empty($request->quantity) ? $request->quantity : null;
        $save->note = !empty($request->note) ? $request->note : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = ReservationDetailEquipments::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getReservationDetailEquipment = ReservationDetailEquipments::find($save->id);

        return response()->json($getReservationDetailEquipment);
    }
}
