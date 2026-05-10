<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ReservationStatuses;
use App\Models\ReservationTypes;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationTypesController extends Controller
{
    private $screen = 'reservation_types';

    public function getReservationTypes(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationTypeId = !empty($request->reservation_type_Id) ? $request->reservation_type_Id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = ReservationTypes::select(
            'reservation_types.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_types.reservation_type', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationStatusId), function ($query) use ($reservationTypeId) {
                return $query->where('reservation_types.uuid', $reservationTypeId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_types.status', $status);
            })
            ->orderBy('id', 'ASC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getReservationType(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationTypeId = !empty($request->reservation_type_id) ? $request->reservation_type_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = ReservationTypes::select(
            'reservation_types.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_types.reservation_type', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationTypeId), function ($query) use ($reservationTypeId) {
                return $query->where('reservation_types.uuid', $reservationTypeId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_types.status', $status);
            })
            ->first();

        return response()->json($out);

    }

    public function setReservationType(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'reservation_type' => ['required'],

        ]);

        if (!empty($request->reservation_type_id)){
            $save = ReservationTypes::where('uuid', $request->reservation_type_id)->first();
        }else{

            $save = new ReservationTypes();
            $save->status = 1;
        }

        $save->reservation_type = !empty($request->reservation_type) ? $request->reservation_type : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = ReservationTypes::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getReservationType = ReservationTypes::find($save->id);

        $out = [
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Reservation Type Has Been updated!',
        ];

        return response()->json($out);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = ReservationTypes::where('uuid', $request->id)->first();
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
