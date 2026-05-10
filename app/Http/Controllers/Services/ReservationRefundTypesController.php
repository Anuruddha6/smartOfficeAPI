<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ReservationStatuses;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ReservationRefundTypesController extends Controller
{
    private $screen = 'Reservation Refund Types';

    public function getReservationRefundTypes(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $reservationStatusId = !empty($request->reservation_status_Id) ? $request->reservation_status_Id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = ReservationStatuses::select(
            'reservation_statuses.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_statuses.reservation_status', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationStatusId), function ($query) use ($reservationStatusId) {
                return $query->where('reservation_statuses.uuid', $reservationStatusId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_statuses.status', $status);
            })
            ->where('reservation_statuses.status', $status)
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
        $reservationStatusId = !empty($request->reservation_status_Id) ? $request->reservation_status_Id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = ReservationStatuses::select(
            'reservation_statuses.*',
        )

            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('reservation_statuses.reservation_status', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($reservationStatusId), function ($query) use ($reservationStatusId) {
                return $query->where('reservation_statuses.uuid', $reservationStatusId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('reservation_statuses.status', $status);
            })

            ->first();

        return response()->json($out);

    }

    public function setReservationRefundType(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'reservation_status' => ['required'],

        ]);

        if (!empty($request->reservation_status_Id)){
            $save = ReservationStatuses::where('uuid', $request->reservation_status_Id)->first();
        }else{

            $save = new ReservationStatuses();
            $save->status = 1;
        }

        $save->reservation_status = !empty($request->reservation_status) ? $request->reservation_status : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = ReservationStatuses::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getReservationStatus = ReservationStatuses::find($save->id);

        $out = [
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Reservation Status Has Been updated!',
        ];

        return response()->json($out);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = ReservationStatuses::where('uuid', $request->id)->first();
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
