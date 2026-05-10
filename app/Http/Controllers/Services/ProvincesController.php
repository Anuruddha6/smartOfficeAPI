<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Provinces;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class ProvincesController extends Controller
{
    private $screen = 'province';

    public function getProvinces(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $provinceId = !empty($request->province_id) ? $request->province_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $get = Provinces::select(
            'provinces.*',
        )
            ->when(!empty($provinceId), function ($query) use ($provinceId) {
                return $query->where('provinces.uuid', $provinceId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('provinces.status', $status);
            })
            ->orderBy('id', 'ASC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getProvince(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $provinceId = !empty($request->province_id) ? $request->province_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $out = Provinces::select(
            'provinces.*',
        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('provinces.province', 'like', '%' . $keyword . '%');
            })

            ->when(!empty($provinceId), function ($query) use ($provinceId) {
                return $query->where('provinces.uuid', $provinceId);
            })
            ->where('provinces.status', $status)
            ->orderBy('id', 'ASC')
            ->first();

        return response()->json($out);

    }

    public function setProvince(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'province_id' => ['required'],
            'district' => ['required', 'max:500'],
        ]);

        if (!empty($request->province_id)){
            $save = Provinces::where('uuid', $request->province_id)->first();
        }else{

            $province = Provinces::where('uuid', $request->province_id)->first();

            $save = new Provinces();
            $save->province_id = $province->id;
            $save->status = 1;
        }

        $save->location = !empty($request->location) ? $request->location : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Provinces::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getProvinces = Provinces::find($save->id);

        return response()->json($getProvinces);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = Provinces::where('uuid', $request->id)->first();
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
