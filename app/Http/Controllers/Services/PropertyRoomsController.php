<?php

namespace App\Http\Controllers\services;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Locations;
use App\Models\Properties;
use App\Models\PropertyRoomEquipments;
use App\Models\PropertyRoomFeatures;
use App\Models\PropertyRoomImages;
use App\Models\PropertyRooms;
use App\Models\Provinces;
use App\Validator\APIValidator;
use Illuminate\Http\Request;

class PropertyRoomsController extends Controller
{
    private $screen = 'property_rooms';

    public function getPropertyRooms(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $thisUserId = $this->userId;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $get = PropertyRooms::select(
            'property_rooms.*',
            'properties.name AS property_name',
            'property_room_images.image AS property_room_primary_image'
        )
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->leftJoin('property_room_images', 'property_rooms.id', 'property_room_images.property_room_id')
            ->with([
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
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.people', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_hour', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_half_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_halfday', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_hourly', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('properties.user_id', $userId);
            }, function ($query) use ($thisUserId) {
                if (empty($this->isAdminCategory)) {
                    $query->where(function ($query) use ($thisUserId) {
                        return $query->where('properties.user_id', $thisUserId);
                    });
                }
                return $query;
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->where('property_room_images.is_primary', $status)
            ->where('properties.status', $status)
            ->where('properties.is_deleted', $isDeleted)
            ->orderBy('id', 'DESC');

        if (!empty($mode) && $mode == 'for_select') {
            $out = $get->get();
        } else {
            $out = $get->paginate($itemsPerPage, ['*'], 'page', $currentPage);
        }

        return response()->json($out);
    }

    public function getPropertyRoom(Request $request){

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isIgnoreStatus = !empty($request->is_ignore_status) ? $request->is_ignore_status : 0;
        $isDeleted = !empty($request->is_deleted) ? $request->is_deleted : 0;


        $out = PropertyRooms::select(
            'property_rooms.*',

        )
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->with([
                'property_room_equipments' => function ($query) {
                    $query->select(
                        'property_room_equipments.*',
                    )->where('property_room_equipments.status', 1);
                },
                'property_room_features' => function ($query) {
                    $query->select(
                        'property_room_features.*',
                    )->where('property_room_features.status', 1);
                },
                'property_room_images' => function ($query) {
                    $query->select(
                        'property_room_images.*',
                    )->where('property_room_images.status', 1)
                    ->orderBy('is_primary', 'DESC');
                }
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.people', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_hour', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_half_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_halfday', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_hourly', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->when(empty($isIgnoreStatus), function ($query) use ($status) {
                return $query->where('property_rooms.status', $status);
            })
            ->first();

        return response()->json($out);

    }

    public function setPropertyRoom(Request $request){
        $out = [];

        APIValidator::validate($request, [
            'property_id' => ['required'],
            'name' => ['required', 'max:500'],
        ]);


        if (!empty($request->property_room_id)){
            $save = PropertyRooms::where('uuid', $request->property_room_id)->first();
        }else{

            $save = new PropertyRooms();
            $save->status = 1;
        }
        $property = Properties::where('uuid', $request->property_id)->first();

        $save->property_id = $property->id;
        $save->name = !empty($request->name) ? $request->name : null;
        $save->people = !empty($request->people) ? $request->people : null;
        $save->price_hour = !empty($request->price_hour) ? $request->price_hour : null;
        $save->price_half_day = !empty($request->price_half_day) ? $request->price_half_day : null;
        $save->price_day = !empty($request->price_day) ? $request->price_day : null;
        $save->clearence_period_halfday = !empty($request->clearence_period_halfday) ? date('H:i:s', strtotime($request->clearence_period_halfday)) : null;
        $save->clearence_period_hourly = !empty($request->clearence_period_hourly) ? date('H:i:s', strtotime($request->clearence_period_hourly)) : null;
        $save->description = !empty($request->description) ? $request->description : null;

        $save->save();


        if (empty($save->uuid)){
            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, $save->id);
            $tProperty = PropertyRooms::find($save->id);
            $tProperty->uuid = $uuId;
            $tProperty->save();
        }

        $getPropertyRoom = PropertyRooms::find($save->id);

        return response()->json($getPropertyRoom);
    }

    public function setStatus(Request $request){
        $out = [];
        $save = PropertyRooms::where('uuid', $request->id)->first();
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

    public function getDetailsForPropertyRoomCreations(Request $request){
        $out = [];

        if (!empty($request->properties)){
            if (!empty($this->isAdminCategory)){
                $out['properties'] = Properties::where('status', 1)->where('is_deleted', 0)->get();
            }else{
                $out['properties'] = Properties::where('user_id', $this->userId)->where('status', 1)->where('is_deleted', 0)->get();
            }

        }
        return response()->json($out);

    }

    public function getDetailsForPropertyRoomEdit(Request $request){
        $out = [];

        $room = [];
        $p = PropertyRooms::select('property_rooms.*', 'properties.user_id')
            ->join('properties', 'properties.id', 'property_rooms.property_id')
            ->where('property_rooms.uuid', $request->property_room_id)
            ->where('properties.status', 1)
            ->where('properties.is_deleted', 0)
            ->first();
        if (!empty($p)){
            if (!empty($request->properties)){
                $out['properties'] = Properties::where('user_id', $p->user_id)->where('status', 1)->where('is_deleted', 0)->get();
            }
            $room = $p;
        }

        $out['room'] = $room;


        return response()->json($out);

    }

    public function getPropertyRoomImages(Request $request)
    {
        $out = [];
        if (!empty($request->property_room_id)){
            $propertyRoom = PropertyRooms::where('uuid', $request->property_room_id)->first();
            $out = PropertyRoomImages::where('property_room_id', $propertyRoom->id)->orderBy('is_primary', 'DESC')->get();
        }

        return response()->json($out);
    }

    public function setPropertyRoomImage(Request $request)
    {
        $out = [];
        if (!empty($request->property_room_id)){
            $propertyRoom = PropertyRooms::where('uuid', $request->property_room_id)->first();
            $propertyRoomId = $propertyRoom->id;

            $isPrimary = 0;
            $pc = PropertyRoomImages::where('property_room_id', $propertyRoomId)->count();
            if (empty($pc)){
                $isPrimary = 1;
            }

            $getCommon = new CommonHelper();
            $uuId = $getCommon->generateUUId($this->screen, 0);

            $set = new PropertyRoomImages();
            $set->uuid = $uuId;
            $set->property_room_id = $propertyRoomId;
            $set->image = !empty($request->image) ? $request->image : null;
            $set->is_primary = $isPrimary;
            $set->status = 1;
            $set->save();



            $out['image'] = $set;
            $out['status'] = 'success';
            $out['message_title'] = 'Success!';
            $out['message_text'] = 'New Property Image Added!';
        }

        return response()->json($out);
    }

    public function deletePropertyRoomImage(Request $request)
    {
        $out = [];
        if (!empty($request->property_room_image_id)){
            PropertyRoomImages::where('uuid', $request->property_room_image_id)->delete();

            $out['status'] = 'success';
            $out['message_title'] = 'Success!';
            $out['message_text'] = 'Property Image Deleted!';
        }

        return response()->json($out);
    }

    public function setPrimaryImage(Request $request)
    {
        $out = [];
        if (!empty($request->property_room_image_id)){
            $getImage = PropertyRoomImages::where('uuid', $request->property_room_image_id)->first();
            if (!empty($getImage)){
                $propertyRoomId = $getImage->property_room_id;
                $getImages = PropertyRoomImages::where('property_room_id', $propertyRoomId)->get();
                foreach ($getImages as $getImage){
                    $imgId = $getImage->id;
                    $u = PropertyRoomImages::find($imgId);
                    $u->is_primary = 0;
                    $u->save();
                }

                $getImage->is_primary = 1;
                $getImage->save();
            }

            $out['status'] = 'success';
            $out['message_title'] = 'Success!';
            $out['message_text'] = 'Primary image has been changed!';
        }

        return response()->json($out);
    }

    public function getPropertyRoomsForHomepage(Request $request){
        $out = [];

        $itemsPerPage = !empty($request->items_per_page) ? $request->items_per_page : $this->defaultItemsPerPage;
        $currentPage = !empty($request->current_page) ? $request->current_page : 0;
        $mode = !empty($request->mode) ? $request->mode : null;

        $thisUserId = $this->userId;

        $keyword = !empty($request->keyword) ? $request->keyword : '';
        $propertyRoomId = !empty($request->property_room_id) ? $request->property_room_id : 0;
        $propertyId = !empty($request->property_id) ? $request->property_id : 0;
        $userId = !empty($request->user_id) ? $request->user_id : 0;
        $status = !empty($request->status) ? $request->status : 1;
        $isDeleted = 0;

        $provinceId = !empty($request->province_id) ? $request->province_id : 0;
        $districtId = !empty($request->district_id) ? $request->district_id : 0;
        $locationId = !empty($request->location_id) ? $request->location_id : 0;
        $people = !empty($request->people) ? $request->people : 0;

        $out = PropertyRooms::select(
            'property_rooms.*',
            'properties.name AS property_name',
            'locations.location',
            'districts.district',
            'provinces.province',
            'property_room_images.image AS property_room_primary_image'
        )
            ->join('properties', 'property_rooms.property_id', 'properties.id')
            ->join('locations', 'properties.location_id', 'locations.id')
            ->join('districts', 'locations.district_id', 'districts.id')
            ->join('provinces', 'districts.province_id', 'provinces.id')
            ->leftJoin('property_room_images', 'property_rooms.id', 'property_room_images.property_room_id')
            ->with([
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
            ])
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->where('property_rooms.name', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.people', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_hour', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_half_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.price_day', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_halfday', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.clearence_period_hourly', 'like', '%' . $keyword . '%')
                    ->orWhere('property_rooms.description', 'like', '%' . $keyword . '%');
            })
            ->when(!empty($propertyRoomId), function ($query) use ($propertyRoomId) {
                return $query->where('property_rooms.uuid', $propertyRoomId);
            })
            ->when(!empty($propertyId), function ($query) use ($propertyId) {
                return $query->where('properties.uuid', $propertyId);
            })
            ->when(!empty($provinceId), function ($query) use ($provinceId) {
                return $query->where('provinces.uuid', $provinceId);
            })
            ->when(!empty($districtId), function ($query) use ($districtId) {
                return $query->where('districts.uuid', $districtId);
            })
            ->when(!empty($locationId), function ($query) use ($locationId) {
                return $query->where('locations.uuid', $locationId);
            })
            ->when(!empty($people), function ($query) use ($people) {
                return $query->where('property_rooms.people', '>=', $people);
            })
            ->where('property_room_images.is_primary', $status)
            ->where('properties.is_verified', $status)
            ->where('properties.status', $status)
            ->where('properties.is_deleted', $isDeleted)
            ->orderBy('id', 'DESC')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return response()->json($out);
    }
}
