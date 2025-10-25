<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\UserRoles;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class UserStasusesController extends Controller
{
    public function getUserRoles(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userRoleId = !empty($request->user_role_id) ? $request->user_role_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = UserRoles::select(
            'user_roles.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_roles.user_role', 'like', '%' . $keyword . '%')
                    ->orWhere('user_roles.display_name', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($userRoleId), function ($query) use ($userRoleId) {
                return $query->where('user_roles.uuid', $userRoleId);
            })

            ->where('user_roles.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getUserRole(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userRoleId = !empty($request->user_role_id) ? $request->user_role_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = UserRoles::select(
            'user_roles.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('user_roles.user_role', 'like', '%' . $keyword . '%')
                    ->orWhere('user_roles.display_name', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($userRoleId), function ($query) use ($userRoleId) {
                return $query->where('user_roles.uuid', $userRoleId);
            })

            ->where('user_roles.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setUserRole(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'user_role' => ['required', 'max:500'],
        ]);

        if (!empty($request->user_role_id)){
            $save = UserRoles::where('uuid', $request->user_role_id)->first();
        }else{

            $save = new UserRoles();
            $save->status = 1;
        }

        $save->user_role = !empty($request->user_role) ? $request->user_role : null;
        $save->display_name = !empty($request->display_name) ? $request->display_name : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $update = UserRoles::find($save->id);
            $update->uuid = $uuId;
            $update->save();
        }

        $getUserRole = UserRoles::find($save->id);

        return response()->json($getUserRole);
    }
}
