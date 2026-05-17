<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Locations;
use App\Models\Properties;
use App\Models\Provinces;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Validator\APIValidator;

class PropertiesController extends Controller
{
    private $screen = 'properties';

    public function getProperties(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;
        $isVerified = !empty($request->is_verified) ? $request->is_verified : 1;

        $get = Properties::select(
            'properties.*',
            'locations.location',
            'districts.district',
            'provinces.province',
        )
            ->join('locations', 'properties.location_id', 'locations.id')
            ->join('districts', 'locations.district_id', 'districts.id')
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->with([
                'property_rooms' => function ($query) {
                    $query->select(
                        'property_rooms.*',
                    )->with([
                        'property_room_equipments' => function ($query) {
                            $query->select(
                                'property_room_equipments.*',
                            )->where('property_room_equipments.status', 1);
                        },
                        'property_room_features' => function ($query) {
                            $query->select(
                                'property_room_features.*',
                            )->where('property_room_features.status', 1);
                        }
                    ])->where('property_rooms.status', 1);
                }
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('properties.name', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_1', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.address_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.town', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.city', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('properties.location_id', $locationId);
            })
            ->when(!empty($isDeleted), function ($query) use ($isDeleted) {
                return $query->where('properties.is_deleted', 1);
            }, function ($query) use ($request){
                return $query->where('properties.is_deleted', 0);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('properties.status', $status);
            })
            ->orderBy('id', 'DESC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getProperty(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;
        $isVerified = !empty($request->is_verified) ? $request->is_verified : 1;

        $out = Properties::select(
            'properties.*',
            'users.first_name',
            'users.last_name',
            'users.email',
            'locations.location',
            'districts.district',
            'provinces.province',
        )
            ->join('users', 'properties.user_id', 'users.id')
            ->join('locations', 'properties.location_id', 'locations.id')
            ->join('districts', 'locations.district_id', 'districts.id')
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->with([
                'property_rooms' => function ($query) {
                    $query->select(
                        'property_rooms.*',
                    )->with([
                        'property_room_equipments' => function ($query) {
                            $query->select(
                                'property_room_equipments.*',
                            )->where('property_room_equipments.status', 1);
                        },
                        'property_room_features' => function ($query) {
                            $query->select(
                                'property_room_features.*',
                            )->where('property_room_features.status', 1);
                        }
                        ,
                        'property_room_images' => function ($query) {
                            $query->select(
                                'property_room_images.*',
                            )->where('property_room_images.status', 1)
                            ->orderBy('property_room_images.is_primary', 'DESC');
                        }
                    ])->where('property_rooms.status', 1);
                }
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('properties.name', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_1', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.phone_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.address_2', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.town', 'like', '%' . $keyword . '%')
                    ->orWhere('properties.city', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('properties.location_id', $locationId);
            })
            ->when(!empty($isDeleted), function ($query) use ($isDeleted) {
                return $query->where('properties.is_deleted', 1);
            }, function ($query) use ($request){
                return $query->where('properties.is_deleted', 0);
            })
            ->first();

        return response()->json($out);

    }

    public function setProperty(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'user_id' => ['required'],
            'location_id' => ['required'],
            'name' => ['required', 'max:500'],
            'property_image' => ['required'],
        ]);


        if (!empty($request->property_id)){
            $save = Properties::where('uuid', $request->property_id)->first();
        }else{

            $u = User::where('uuid', $request->user_id)->first();

            $save = new Properties();
            $save->user_id = $u->id;
            $save->is_verified = 0;
            $save->is_deleted = 0;
            $save->status = 1;
        }

        $location = Locations::where('uuid', $request->location_id)->first();

        $save->location_id = $location->id;
        $save->name = !empty($request->name) ? $request->name : null;
        $save->phone_1 = !empty($request->phone_1) ? $request->phone_1 : null;
        $save->phone_2 = !empty($request->phone_2) ? $request->phone_2 : null;
        $save->street_address = !empty($request->street_address) ? $request->street_address : null;
        $save->address_2 = !empty($request->address_2) ? $request->address_2 : null;
        $save->town = !empty($request->town) ? $request->town : null;
        $save->city = !empty($request->city) ? $request->city : null;
        $save->property_image = !empty($request->property_image) ? $request->property_image : 'default-property.jpg';

        $save->save();


        if (empty($save->uuid) && empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = Properties::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getProperty = Properties::find($save->id);

        return response()->json($getProperty);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = Properties::where('uuid', $request->id)->first();
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

    public function getDetailsForPropertyCreations(Request $request){
        $out = [];
        if (!empty($request->vendors)){
             if (!empty($this->isSuperAdmin)){
                 $out['vendor_mode'] = 'super_admin';
                 $out['vendors'] = User::whereIn('user_role_id', [1, 2, 3])->where('status', 1)->where('is_deleted', 0)->get();
             }
             elseif (!empty($this->isAdmin)){
                 $out['vendor_mode'] = 'admin';
                 $out['vendors'] = User::whereIn('user_role_id', [3])->where('status', 1)->where('is_deleted', 0)->get();
             }
             elseif (!empty($this->isVendor)){
                 $out['vendor_mode'] = 'vendor';
                 $out['vendors'] = $this->userUUId;
             }
        }

        return response()->json($out);

    }

    public function getDetailsForPropertyEdit(Request $request){
        $out = [];
        if (!empty($request->vendors)){
            if (!empty($this->isSuperAdmin)){
                $out['vendor_mode'] = 'super_admin';
                $out['vendors'] = User::whereIn('user_role_id', [1, 2, 3])->where('status', 1)->where('is_deleted', 0)->get();
            }
            elseif (!empty($this->isAdmin)){
                $out['vendor_mode'] = 'admin';
                $out['vendors'] = User::whereIn('user_role_id', [3])->where('status', 1)->where('is_deleted', 0)->get();
            }
            elseif (!empty($this->isVendor)){
                $out['vendor_mode'] = 'vendor';
                $out['vendors'] = $this->userUUId;
            }
        }

        $property = [];
        $p = Properties::where('uuid', $request->property_id)->first();
        if (!empty($p)){
            $locationId = $p->location_id;
            $getLocation = Locations::where('id', $locationId)->first();

            $districtId = $getLocation->district_id;
            $getDistrict = Districts::where('id', $districtId)->first();

            $provinceId = $getDistrict->province_id;

            $getProvinces = Provinces::where('status', 1)->get();
            $getDistricts = Districts::where('province_id', $provinceId)->where('status', 1)->get();
            $getLocations = Locations::where('district_id', $districtId)->where('status', 1)->get();

            $p['district_id'] = $districtId;
            $p['province_id'] = $provinceId;
            $p['locations'] = $getLocations;
            $p['districts'] = $getDistricts;
            $p['provinces'] = $getProvinces;
            $property = $p;
        }

        $out['property'] = $property;


        return response()->json($out);

    }

    public function setVerify(Request $request){

        $save = Properties::where('uuid', $request->property_id)->first();

        if (!empty($save)){
            $save->is_verified = 1;
            $save->verified_by = $this->userId;
            $save->verified_at = $this->dbInsertTime();
            $save->save();
        }

        $out = [
            'status' => 'success',
            'message_title' => 'Success!',
            'message_text' => 'Property Has Been Verified!',
        ];

        return response()->json($out);
    }
}







