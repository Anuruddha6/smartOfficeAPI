<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Helpers\DBHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRoles;
use App\Validator\APIValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    private $screen = 'users';

    public function getUsers(Request $request){

        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $userId = !empty($request->user_id) ? $request->user_id : 0;
        $publicKey = !empty($request->public_key) ? $request->public_key : 0;
        $userRoleId = !empty($request->user_role_id) ? $request->user_role_id : 0;
        $isDeletedOnly = !empty($request->is_deleted) ? $request->is_deleted : 0;
        $status = !empty($request->status) ? $request->status : 1;

        $out = User::select(
            'users.*',
            'user_roles.user_role',
            'user_roles.display_name AS user_role_display_name',
        )
            ->join('user_roles', 'users.user_role_id', 'user_roles.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where(DB::raw(DBHelper::dbConcat('users', 'first_name','users', 'last_name')), 'like', '%' . $keyword . '%')
                    ->orWhere('users.public_key', 'like', '%' . $keyword . '%')
                    ->orWhere('users.email', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('users.uuid', $userId);
            })
            ->when(!empty($publicKey), function ($query) use ($publicKey) {
                return $query->where('users.public_key', $publicKey);
            })
            ->when(!empty($userRoleId), function ($query) use ($userRoleId) {
                return $query->where('users.user_role_id', $userRoleId);
            })
            ->when(!empty($isDeletedOnly), function ($query) use ($isDeletedOnly) {
                return $query->where('users.is_deleted', 1);
            }, function ($query) use ($request){
                return $query->where('users.is_deleted', 0);
            })
            ->where('users.status', $status)
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }


    public function setUser(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $isNewUser = 0;
        if (!empty($request->user_id)){
            $user = User::where('uuid', $request->user_id)->first();
        }else{

            $isNewUser = 1;

            $user = new User();
            $user->password = Hash::make($request->password);
            $user->is_deleted = 0;
            $user->status = 1;
            $user->email = $request->email;
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_role_id  = !empty($request->user_role_id) ? $request->user_role_id : 3;

        $user->save();

        //Set Public Key
        if (!empty($isNewUser) && $user){
            $keyUser = User::find($user->id);
            $key = $keyUser->createToken('public' .'-'. $user->id)->plainTextToken;
            $publicKey = explode('|', $key)[1];
            $keyUser->public_key = $publicKey;
            $keyUser->save();
        }

        if (empty($request->uuid) && empty($user->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $user->id);
            $tuser = User::find($user->id);
            $tuser->uuid = $uuId;
            $tuser->save();
        }

        $getUser = User::find($user->id);

        return response()->json($getUser);
    }
}
