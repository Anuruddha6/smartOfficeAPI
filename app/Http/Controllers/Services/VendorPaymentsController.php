<?php

namespace App\Http\Controllers\Services;

use App\Helpers\CommonHelper;
use App\Helpers\UsersHelper;
use App\Http\Controllers\Controller;
use App\Models\PropertyRoomEquipments;
use App\Models\PropertyRooms;
use App\Models\User;
use App\Models\VendorPayments;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class VendorPaymentsController extends Controller
{
    private $screen = 'vendor_payments';

    public function getVendorPayments(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $vendorPaymentId = !empty($request->vendor_payment_id) ? $request->vendor_payment_id : 0;
        $vendorId = !empty($request->vendor_id) ? $request->vendor_id : 0;
        $bankName = !empty($request->bank_name) ? $request->bank_name : null;
        $branchName = !empty($request->branch_name) ? $request->branch_name : null;
        $accountName = !empty($request->account_name) ? $request->account_name : null;
        $accountName = !empty($request->account_number) ? $request->account_number : null;
        $status = !empty($request->status) ? $request->status : 1;

        $out = VendorPayments::select(
            'vendor_payments.*',

        )
            ->join('users', 'vendor_payments.vendor_id', 'users.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('vendor_payments.bank_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.branch_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.account_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.account_number', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($vendorPaymentId), function ($query) use ($vendorPaymentId) {
                return $query->where('vendor_payments.uuid', $vendorPaymentId);
            })
            ->when(!empty($vendorId), function ($query) use ($vendorId) {
                return $query->where('users.uuid', $vendorId);
            })
            ->where('vendor_payments.status', $status)
            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }

    public function getVendorPayment(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $vendorPaymentId = !empty($request->vendor_payment_id) ? $request->vendor_payment_id : 0;
        $vendorId = !empty($request->vendor_id) ? $request->vendor_id : 0;
        $bankName = !empty($request->bank_name) ? $request->bank_name : null;
        $branchName = !empty($request->branch_name) ? $request->branch_name : null;
        $accountName = !empty($request->account_name) ? $request->account_name : null;
        $accountName = !empty($request->account_number) ? $request->account_number : null;
        $status = !empty($request->status) ? $request->status : 1;

        $out = VendorPayments::select(
            'vendor_payments.*',

        )
            ->join('users', 'vendor_payments.vendor_id', 'users.id')
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('vendor_payments.bank_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.branch_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.account_name', 'like', '%' . $keyword . '%')
                    ->orWhere('vendor_payments.account_number', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($vendorPaymentId), function ($query) use ($vendorPaymentId) {
                return $query->where('vendor_payments.uuid', $vendorPaymentId);
            })
            ->when(!empty($vendorId), function ($query) use ($vendorId) {
                return $query->where('users.uuid', $vendorId);
            })
            ->where('vendor_payments.status', $status)
            ->first();

        return response()->json($out);

    }

    public function setVendorPayment(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'vendor_id' => ['required'],
            'amount' => ['required', 'max:500'],
            'bank_name' => ['required', 'max:500'],
            'branch_name' => ['required', 'max:500'],
            'account_name' => ['required', 'max:500'],
            'account_number' => ['required', 'max:500'],
        ]);

        if (!empty($request->vendor_payment_id)){
            $save = VendorPayments::where('uuid', $request->vendor_payment_id)->first();
        }else{

            $vendor = User::where('uuid', $request->vendor_id)->first();

            $save = new VendorPayments();
            $save->vendor_id = $vendor->id;
            $save->paid_by = $this->userId;
            $save->paid_time = $this->dbInsertTime();
            $save->status = 1;
        }

        $save->amount = !empty($request->amount) ? $request->amount : null;
        $save->bank_name = !empty($request->bank_name) ? $request->bank_name : null;
        $save->branch_name = !empty($request->branch_name) ? $request->branch_name : null;
        $save->account_name = !empty($request->account_name) ? $request->account_name : null;
        $save->account_number = !empty($request->account_number) ? $request->account_number : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $update = VendorPayments::find($save->id);
            $update->uuid = $uuId;
            $update->save();
        }

        $getVendorPayment = VendorPayments::find($save->id);

        return response()->json($getVendorPayment);
    }
}
