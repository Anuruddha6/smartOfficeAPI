<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomFeatures;
use App\Models\PropertyRooms;
use App\Models\ReservationDetails;
use App\Models\Reservations;
use App\Models\ReservationTypes;
use App\Models\User;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationDetailsController extends Controller
{
    private $screen = 'reservation_details';
    public function getReservationDetails(Request $request){
        $out = [];

        //Search
        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationDetailId = !empty($request->reservation_detail_id) ? $request->reservation_detail_id : 0;
        $reservationId = !empty($request->reservation_id) ? $request->reservation_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;

        $out = ReservationDetails::select(
            'reservation_details.*',

        )
            ->join('reservations', 'reservation_details.reservation_id', 'reservations.id')
            ->join('property_rooms', 'reservation_details.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')

            ->with([
                'reservation_detail_equipments' => function ($query) {
                    $query->select(
                        'reservation_detail_equipments.*',
                    )->where('reservation_detail_equipments.status', 1);
                },
                'reservation_detail_features' => function ($query) {
                    $query->select(
                        'reservation_detail_features.*',
                    )->where('reservation_detail_features.status', 1);
                }
            ])

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservations.reservation_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.reservation_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.property_room_id', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationId), function ($query) use ($reservationId) {
                return $query->where('reservations.uuid', $reservationId);
            })
            ->when(!empty($reservationDetailId), function ($query) use ($reservationDetailId) {
                return $query->where('reservation_details.uuid', $reservationDetailId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('properties.uuid', $propertyRoomId);
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('reservations.user_id', $userId);
            })

            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getReservationDetail(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationDetailId = !empty($request->reservation_detail_id) ? $request->reservation_detail_id : 0;
        $reservationId = !empty($request->reservation_id) ? $request->reservation_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;

        $out = ReservationDetails::select(
            'reservation_details.*',

        )
            ->join('reservations', 'reservation_details.reservation_id', 'reservations.id')
            ->join('property_rooms', 'reservation_details.property_room_id', 'property_rooms.id')
            ->join('properties', 'property_rooms.property_id', 'properties.id')

            ->with([
                'reservation_detail_equipments' => function ($query) {
                    $query->select(
                        'reservation_detail_equipments.*',
                    )->where('reservation_detail_equipments.status', 1);
                },
                'reservation_detail_features' => function ($query) {
                    $query->select(
                        'reservation_detail_features.*',
                    )->where('reservation_detail_features.status', 1);
                }
            ])

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservations.reservation_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.reservation_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.property_room_id', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationId), function ($query) use ($reservationId) {
                return $query->where('reservations.uuid', $reservationId);
            })
            ->when(!empty($reservationDetailId), function ($query) use ($reservationDetailId) {
                return $query->where('reservation_details.uuid', $reservationDetailId);
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('properties.uuid', $propertyRoomId);
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('reservations.user_id', $userId);
            })

            ->first();

        return response()->json($out);

    }

    public function setReservationDetail(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'reservation_id' => ['required'],
            'property_room_id' => ['required'],
            'reservation_type_id' => ['required'],
            'reservation_name' => ['required', 'max:500'],
        ]);

        //update
        if (!empty($request->reservation_detail_id)){
            $save = ReservationDetails::where('uuid', $request->reservation_detail_id)->first();
        }else{

            //create
            $reservation = Reservations::where('uuid', $request->reservation_id)->first();
            $propertyRoom = PropertyRooms::where('uuid', $request->property_room_id)->first();

            $clearanceTime = null;
            if ($request->reservation_type_id == 2){
                $clearanceTime = $propertyRoom->clearence_period_halfday;
            }elseif($request->reservation_type_id == 3){
                $clearanceTime = $propertyRoom->clearence_period_hourly;
            }

            $save = new ReservationDetails();
            $save->reservation_id = $reservation->id;
            $save->property_room_id = $propertyRoom->id;
            $save->clearance_time = $clearanceTime;
            $save->status= 1;
        }

        $reservationType = ReservationTypes::where('uuid', $request->reservation_type_id)->first();
        $save->reservation_type_id = $reservationType->id;

        $save->reservation_name = !empty($request->reservation_name) ? $request->reservation_name : null;
        $save->head_count = !empty($request->head_count) ? $request->head_count : null;
        $save->start_time = !empty($request->start_time) ? $request->start_time : null;
        $save->end_time = !empty($request->end_time) ? $request->end_time : null;
        $save->clearance_time = !empty($request->clearance_time) ? $request->clearance_time : null;
        $save->note = !empty($request->note) ? $request->note : null;
        $save->price = !empty($request->price) ? $request->price : null;

        $save->save();

        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $update = ReservationDetails::find($save->id);
            $update->uuid = $uuId;
            $update->save();
        }

        $getReservationDetail = ReservationDetails::find($save->id);

        return response()->json($getReservationDetail);
    }
}
