<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomFeatures;
use App\Models\PropertyRooms;
use App\Models\ReservationDetails;
use App\Models\Reservations;
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
        $reservationId = !empty($request->reservation_id) ? $request->reservation_id : 0;
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;
        $reservationStatusId = !empty($request->reservation_status_id) ? $request->reservation_status_id : 0;


        $out = PropertyRoomFeatures::select(
            'reservation_details.*',

        )
            ->join('users', 'reservations.user_id', 'users.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservations.first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.email', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.address_2', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.town', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.city', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.district_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.note', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.stripe_payment_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.stripe_charge_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.xero_invoice_status', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.xero_invoice_id', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationId), function ($query) use ($reservationId) {
                return $query->where('reservations.uuid', $reservationId);
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('users.uuid', $userId);
            })
            ->when(!empty($reservationStatusId), function ($query) use ($reservationStatusId) {
                return $query->where('reservations.reservation_status_id', $reservationStatusId);
            })

            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getReservationDetail(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationId = !empty($request->reservation_id) ? $request->reservation_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;
        $reservationStatusId = !empty($request->reservation_status_id) ? $request->reservation_status_id : 0;


        $out = PropertyRoomFeatures::select(
            'reservations.*',

        )
            ->join('users', 'reservations.user_id', 'users.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservations.first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.email', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.address_2', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.town', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.city', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.district_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.note', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.stripe_payment_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.stripe_charge_id', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.xero_invoice_status', 'like', '%' . $keyword . '%')
                    ->orWhere('reservations.xero_invoice_id', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationId), function ($query) use ($reservationId) {
                return $query->where('reservations.uuid', $reservationId);
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('users.uuid', $userId);
            })
            ->when(!empty($reservationStatusId), function ($query) use ($reservationStatusId) {
                return $query->where('reservations.reservation_status_id', $reservationStatusId);
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
// `reservation_type_id`, `reservation_name`, `head_count`, `start_time`, `end_time`, `clearance_time`, `note`, `price`, `is_refunded`, `refunded_by`, `refunded_at`, `reservation_refund_id`
        $save->reservation_type_id = !empty($request->reservation_type_id) ? $request->reservation_type_id : null;
        $save->reservation_name = !empty($request->reservation_name) ? $request->reservation_name : null;
        $save->head_count = !empty($request->head_count) ? $request->head_count : null;
        $save->start_time = !empty($request->start_time) ? $request->start_time : null;
        $save->end_time = !empty($request->end_time) ? $request->end_time : null;
        $save->address_2 = !empty($request->address_2) ? $request->address_2 : null;
        $save->clearance_time = !empty($request->clearance_time) ? $request->clearance_time : null;
        $save->note = !empty($request->note) ? $request->note : null;
        $save->price = !empty($request->price) ? $request->price : null;
        $save->is_refunded = !empty($request->is_refunded) ? $request->is_refunded : null;
        $save->refunded_by = !empty($request->refunded_by) ? $request->refunded_by : null;
        $save->refunded_at = !empty($request->refunded_at) ? $request->refunded_at : null;
        $save->coupon = !empty($request->coupon) ? $request->coupon : null;
        $save->reservation_refund_id = !empty($request->reservation_refund_id) ? $request->reservation_refund_id : null;

        $save->save();

        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $update = ReservationDetails::find($save->id);
            $update->reservation_detail_id = $uuId;
            $update->save();
        }

        $getReservationDetail = ReservationDetails::find($save->id);

        return response()->json($getReservationDetail);
    }
}
