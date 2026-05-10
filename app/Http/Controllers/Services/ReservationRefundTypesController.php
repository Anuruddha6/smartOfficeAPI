<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ReservationRefundTypes;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationRefundTypesController extends Controller
{
    private $screen = 'reservation_refund_types';

    public function getReservationRefundTypes(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationRefundTypeId = !empty($request->reservation_refund_types_id) ? $request->reservation_refund_types_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = ReservationRefundTypes::select(
            'reservation_refund_types.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_refund_types.reservation_refund_type', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationRefundTypeId), function ($query) use ($reservationRefundTypeId) {
                return $query->where('reservation_refund_types.uuid', $reservationRefundTypeId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_refund_types.status', $status);
            })
            ->orderBy('id', 'ASC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getReservationRefundType(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationRefundTypeId = !empty($request->reservation_refund_type_id) ? $request->reservation_refund_type_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = ReservationRefundTypes::select(
            'reservation_refund_types.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_refund_types.reservation_refund_type', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationRefundTypeId), function ($query) use ($reservationRefundTypeId) {
                return $query->where('reservation_refund_types.uuid', $reservationRefundTypeId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_refund_types.status', $status);
            })
            ->first();

        return response()->json($out);

    }

    public function setReservationRefundType(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'reservation_refund_type' => ['required'],

        ]);

        if (!empty($request->reservation_refund_type_id)){
            $save = ReservationRefundTypes::where('uuid', $request->reservation_refund_type_id)->first();
        }else{

            $save = new ReservationRefundTypes();
            $save->status = 1;
        }

        $save->reservation_refund_type = !empty($request->reservation_refund_type) ? $request->reservation_refund_type : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = ReservationRefundTypes::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $out = [
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Reservation Refund Type Has Been updated!',
        ];

        return response()->json($out);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = ReservationRefundTypes::where('uuid', $request->id)->first();
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
