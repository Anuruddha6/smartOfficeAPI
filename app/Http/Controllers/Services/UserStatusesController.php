<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\UserStatuses;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class UserStatusesController extends Controller
{
    private $screen = 'user_statuses';
    public function getUserStatuses(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userStatusId = !empty($request->user_status_id) ? $request->user_status_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = UserStatuses::class::select(
            'user_statuses.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_statuses.user_status', 'like', '%' . $keyword . '%');

            })
            ->when(!empty($userStatusId), function ($query) use ($userStatusId) {
                return $query->where('user_statuses.uuid', $userStatusId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('user_statuses.status', $status);
            })
            ->orderBy('id', 'ASC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getUserStatus(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userStatusId = !empty($request->user_status_id) ? $request->user_status_id : 0;


        $out = UserStatuses::class::select(
            'user_statuses.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_statuses.user_status', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($userStatusId), function ($query) use ($userStatusId) {
                return $query->where('user_statuses.uuid', $userStatusId);
            })
            ->first();

        return response()->json($out);

    }

    public function setStatus(Request $request){
        $out = [];
        $save = UserStatuses::where('uuid', $request->id)->first();
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
