<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomFeatures;
use App\Models\PropertyRooms;
use App\Models\Reservations;
use App\Models\User;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    private $screen = 'reservations';
    public function getReservations(Request $request){
        $out = [];
        //Search
        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

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

            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getReservation(Request $request){

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

    public function setReservation(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'user_id' => ['required'],
            'first_name' => ['required', 'max:500'],
            'last_name' => ['required', 'max:500'],
            'email' => ['required', 'max:500'],
            'phone' => ['required', 'max:500'],
            'street_address' => ['required', 'max:500'],
        ]);
        //update
        if (!empty($request->reservation_id)){
            $save = Reservations::where('uuid', $request->reservation_id)->first();
        }else{
            //create
            $user = User::where('uuid', $request->user_id)->first();

            $save = new Reservations();
            $save->user_id = $user->id;
            $save->reservation_status_id = 2;
        }

        $save->first_name = !empty($request->first_name) ? $request->first_name : null;
        $save->last_name = !empty($request->last_name) ? $request->last_name : null;
        $save->email = !empty($request->email) ? $request->email : null;
        $save->phone = !empty($request->phone) ? $request->phone : null;
        $save->street_address = !empty($request->street_address) ? $request->street_address : null;
        $save->address_2 = !empty($request->address_2) ? $request->address_2 : null;
        $save->town = !empty($request->town) ? $request->town : null;
        $save->city = !empty($request->city) ? $request->city : null;
        $save->district_id = !empty($request->district_id) ? $request->district_id : null;
        $save->payment_method_id = !empty($request->payment_method_id) ? $request->payment_method_id : null;
        $save->note = !empty($request->note) ? $request->note : null;
        $save->coupon_id = !empty($request->coupon_id) ? $request->coupon_id : null;
        $save->coupon = !empty($request->coupon) ? $request->coupon : null;
        $save->discount_type = !empty($request->discount_type) ? $request->discount_type : null;
        $save->discount = !empty($request->discount) ? $request->discount : null;
        $save->discount_value = !empty($request->discount_value) ? $request->discount_value : null;
        $save->vat_precentage = !empty($request->vat_precentage) ? $request->vat_precentage : null;
        $save->vat = !empty($request->vat) ? $request->vat : null;

        $save->save();

        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Reservations::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getReservation = Reservations::find($save->id);

        return response()->json($getReservation);
    }
}
