<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethods;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    private $screen = 'payment_methods';

    public function getPaymentMethods(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $paymentMethodId = !empty($request->payment_method_id) ? $request->payment_method_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;


        $out = PaymentMethods::select(
            'payment_methods.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('payment_methods.payment_method', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($paymentMethodId), function ($query) use ($paymentMethodId) {
                return $query->where('payment_methods.uuid', $paymentMethodId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('payment_methods.status', $status);
            })
            ->orderBy('id', 'ASC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getPaymentMethod(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $paymentMethodId = !empty($request->payment_method_id) ? $request->payment_method_id : 0;
        $status = !empty($request->status) ? $request->status : 1;


        $out = PaymentMethods::select(
            'payment_methods.*',

        )
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('payment_methods.payment_method', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($paymentMethodId), function ($query) use ($paymentMethodId) {
                return $query->where('payment_methods.uuid', $paymentMethodId);
            })

            ->first();

        return response()->json($out);

    }

    public function setPaymentMethod(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'payment_method' => ['required'],
        ]);

        //Edit
        if (!empty($request->payment_method_id)){
            $save = PaymentMethods::where('uuid', $request->payment_method_id)->first();
        }else{

            $save = new PaymentMethods();
            $save->status = 1;
        }

        $save->payment_method = !empty($request->payment_method) ? $request->payment_method : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = PaymentMethods::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getPaymentMethod = PaymentMethods::find($save->id);

        return response()->json($getPaymentMethod);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = PaymentMethods::where('uuid', $request->id)->first();
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
