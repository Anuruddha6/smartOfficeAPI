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

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userStatusId = !empty($request->user_status_id) ? $request->user_status_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = UserStatuses::class::select(
            'user_statuses.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_statuses.user_status', 'like', '%' . $keyword . '%');

            })
            ->when(!empty($userStatusId), function ($query) use ($userStatusId) {
                return $query->where('user_statuses.uuid', $userStatusId);
            })

            ->where('user_statuses.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getUserStatus(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userStatusId = !empty($request->user_status_id) ? $request->user_status_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = UserStatuses::class::select(
            'user_statuses.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_statuses.user_status', 'like', '%' . $keyword . '%');

            })
            ->when(!empty($userStatusId), function ($query) use ($userStatusId) {
                return $query->where('user_statuses.uuid', $userStatusId);
            })

            ->where('user_statuses.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setUserStatus(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'users_status' => ['required', 'max:500'],
        ]);

        if (!empty($request->users_status_id)){
            $save = UserStatuses::where('uuid', $request->users_status_id)->first();
        }else{

            $save = new UserStatuses();
            $save->status = 1;
        }

        $save->users_status = !empty($request->users_status) ? $request->users_status : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $update = UserStatuses::find($save->id);
            $update->uuid = $uuId;
            $update->save();
        }

        $getUserStatus = UserStatuses::find($save->id);

        return response()->json($getUserStatus);
    }
}
