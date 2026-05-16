<?php

use App\Models\ReservationDetailEquipments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//A
use App\Http\Controllers\Services\ApplicationSettingsController;

//d
use App\Http\Controllers\Services\DistrictsController;

//L
use App\Http\Controllers\Services\LocationsController;

//P
use App\Http\Controllers\Services\PaymentMethodsController;
use App\Http\Controllers\Services\PropertiesController;
use App\Http\Controllers\Services\PropertyRoomsController;
use App\Http\Controllers\Services\PropertyRoomEquipmentsController;
use App\Http\Controllers\Services\PropertyRoomFeaturesController;
use App\Http\Controllers\Services\ProvincesController;


//R
use App\Http\Controllers\Services\ReservationsController;
use App\Http\Controllers\Services\ReservationDetailsController;
use App\Http\Controllers\Services\ReservationDetailEquipmentsController;
use App\Http\Controllers\Services\ReservationStatusesController;
use App\Http\Controllers\Services\ReservationTypesController;
use App\Http\Controllers\Services\ReservationRefundTypesController;

//U
use App\Http\Controllers\Services\UsersController;
use App\Http\Controllers\Services\UserRolesController;
use App\Http\Controllers\Services\UserStatusesController;

//v
use App\Http\Controllers\Services\VendorPaymentsController;

Route::post('/Users/setUser', [UsersController::class, 'setUser'])->name('setUser');
Route::post('/Users/loginUser', [UsersController::class, 'loginUser'])->name('loginUser');
Route::post('/Users/test', [UsersController::class, 'test'])->name('test');

// A
Route::prefix('ApplicationSettings')->group(function (){
    Route::post('/getApplicationSetting', [ApplicationSettingsController::class, 'getApplicationSetting'])->name('ApplicationSettings.getApplicationSetting');
});

// D
Route::prefix('Districts')->group(function (){
    Route::post('/getDistricts', [DistrictsController::class, 'getDistricts'])->name('Districts.getDistricts');
    Route::post('/getDistrict', [DistrictsController::class, 'getDistrict'])->name('Districts.getDistrict');
});


// L
Route::prefix('Locations')->group(function (){
    Route::post('/getLocations', [LocationsController::class, 'getLocations'])->name('Locations.getLocations');
    Route::post('/getLocation', [LocationsController::class, 'getLocation'])->name('Locations.getLocation');
});

// P
Route::prefix('Properties')->group(function (){
    Route::post('/getProperties', [PropertiesController::class, 'getProperties'])->name('Properties.getProperties');
    Route::post('/getProperty', [PropertiesController::class, 'getProperty'])->name('Properties.getProperty');
});


Route::prefix('PropertyRooms')->group(function (){
    Route::post('/getPropertyRooms', [PropertyRoomsController::class, 'getPropertyRooms'])->name('propertyRooms.getPropertyRooms');
    Route::post('/getPropertyRoom', [PropertyRoomsController::class, 'getPropertyRoom'])->name('propertyRooms.getPropertyRoom');
});

Route::prefix('PropertyRoomEquipments')->group(function (){
    Route::post('/getPropertyRoomEquipments', [PropertyRoomEquipmentsController::class, 'getPropertyRoomEquipments'])->name('propertyRoomEquipments.getPropertyRoomEquipments');
    Route::post('/getPropertyRoomEquipment', [PropertyRoomEquipmentsController::class, 'getPropertyRoomEquipment'])->name('propertyRoomEquipments.getPropertyRoomEquipment');
});

Route::prefix('PropertyRoomFeatures')->group(function (){
    Route::post('/getPropertyRoomFeatures', [PropertyRoomFeaturesController::class, 'getPropertyRoomFeatures'])->name('propertyRoomFeatures.getPropertyRoomFeatures');
    Route::post('/getPropertyRoomFeature', [PropertyRoomFeaturesController::class, 'getPropertyRoomFeature'])->name('propertyRoomFeatures.getPropertyRoomFeature');
});

Route::prefix('Provinces')->group(function (){
    Route::post('/getProvinces', [ProvincesController::class, 'getProvinces'])->name('Provinces.getProvinces');
    Route::post('/getProvince', [ProvincesController::class, 'getProvince'])->name('Provinces.getProvince');
});


// Auth Routes
Route::middleware('auth:sanctum')->group(function () {

    // A
    Route::prefix('ApplicationSettings')->group(function (){
        Route::post('/setApplicationSetting', [ApplicationSettingsController::class, 'setApplicationSetting'])->name('ApplicationSettings.setApplicationSetting');
    });

    // D
    Route::prefix('Districts')->group(function (){
        Route::post('/setDistrict', [DistrictsController::class, 'setDistrict'])->name('districts.setDistrict');
        Route::post('/setStatus', [DistrictsController::class, 'setStatus'])->name('districts.setStatus');
    });

    // L
    Route::prefix('Locations')->group(function (){
        Route::post('/setLocation', [LocationsController::class, 'setLocation'])->name('locations.setLocation');
        Route::post('/setStatus', [LocationsController::class, 'setStatus'])->name('locations.setStatus');
    });

    // P
    Route::prefix('Properties')->group(function (){
        Route::post('/setProperty', [PropertiesController::class, 'setProperty'])->name('properties.setProperty');
        Route::post('/setStatus', [PropertiesController::class, 'setStatus'])->name('properties.setStatus');
        Route::post('/getDetailsForPropertyCreations', [PropertiesController::class, 'getDetailsForPropertyCreations'])->name('properties.getDetailsForPropertyCreations');
        Route::post('/getDetailsForPropertyEdit', [PropertiesController::class, 'getDetailsForPropertyEdit'])->name('properties.getDetailsForPropertyEdit');
    });

    Route::prefix('PropertyRooms')->group(function (){
        Route::post('/setPropertyRoom', [PropertyRoomsController::class, 'setPropertyRoom'])->name('propertyRooms.setPropertyRoom');
        Route::post('/setStatus', [PropertyRoomsController::class, 'setStatus'])->name('propertyRooms.setStatus');
        Route::post('/getDetailsForPropertyRoomCreations', [PropertyRoomsController::class, 'getDetailsForPropertyRoomCreations'])->name('propertyRooms.getDetailsForPropertyRoomCreations');
        Route::post('/getDetailsForPropertyRoomEdit', [PropertyRoomsController::class, 'getDetailsForPropertyRoomEdit'])->name('propertyRooms.getDetailsForPropertyRoomEdit');
        Route::post('/getPropertyRoomImages', [PropertyRoomsController::class, 'getPropertyRoomImages'])->name('propertyRooms.getPropertyRoomImages');
        Route::post('/setPropertyRoomImage', [PropertyRoomsController::class, 'setPropertyRoomImage'])->name('propertyRooms.setPropertyRoomImage');
        Route::post('/deletePropertyRoomImage', [PropertyRoomsController::class, 'deletePropertyRoomImage'])->name('propertyRooms.deletePropertyRoomImage');
        Route::post('/setPrimaryImage', [PropertyRoomsController::class, 'setPrimaryImage'])->name('propertyRooms.setPrimaryImage');

    });


    Route::prefix('PropertyRoomEquipments')->group(function (){
        Route::post('/setPropertyRoomEquipment', [PropertyRoomEquipmentsController::class, 'setPropertyRoomEquipment'])->name('propertyRoomEquipments.setPropertyRoomEquipment');
        Route::post('/setStatus', [PropertyRoomEquipmentsController::class, 'setStatus'])->name('propertyRoomEquipments.setStatus');
    });

    Route::prefix('PropertyRoomFeatures')->group(function (){
        Route::post('/setPropertyRoomFeature', [PropertyRoomFeaturesController::class, 'setPropertyRoomFeature'])->name('propertyRoomFeatures.setPropertyRoomFeature');
        Route::post('/setStatus', [PropertyRoomFeaturesController::class, 'setStatus'])->name('propertyRoomFeatures.setStatus');
    });

    Route::prefix('PaymentMethods')->group(function (){
        Route::post('/getPaymentMethods', [PaymentMethodsController::class, 'getPaymentMethods'])->name('paymentMethods.getPaymentMethods');
        Route::post('/getPaymentMethod', [PaymentMethodsController::class, 'getPaymentMethod'])->name('paymentMethods.getPaymentMethod');
        Route::post('/setPaymentMethod', [PaymentMethodsController::class, 'setPaymentMethod'])->name('paymentMethods.setPaymentMethod');
        Route::post('/setStatus', [PaymentMethodsController::class, 'setStatus'])->name('paymentMethods.setStatus');
    });


    Route::prefix('Provinces')->group(function (){
        Route::post('/setProvince', [ProvincesController::class, 'setProvince'])->name('provinces.setProvince');
        Route::post('/setStatus', [ProvincesController::class, 'setStatus'])->name('provinces.setStatus');
    });

    // R
    Route::prefix('Reservations')->group(function (){
        Route::post('/getReservations', [ReservationsController::class, 'getReservations'])->name('Reservations.getReservations');
        Route::post('/getReservation', [ReservationsController::class, 'getReservation'])->name('Reservations.getReservation');
        Route::post('/setReservation', [ReservationsController::class, 'setReservation'])->name('Reservations.setReservation');
    });

    Route::prefix('ReservationDetails')->group(function (){
        Route::post('/getReservationDetails', [ReservationDetailsController::class, 'getReservationDetails'])->name('ReservationDetails.getReservationDetails');
        Route::post('/getReservationDetail', [ReservationDetailsController::class, 'getReservationDetail'])->name('ReservationDetails.getReservationDetail');
        Route::post('/setReservationDetail', [ReservationDetailsController::class, 'setReservationDetail'])->name('ReservationDetails.setReservationDetail');
    });

    Route::prefix('ReservationDetailEquipments')->group(function (){
        Route::post('/getReservationDetailEquipments', [ReservationDetailEquipmentsController::class, 'getReservationDetailEquipments'])->name('ReservationDetailEquipments.getReservationDetailEquipments');
        Route::post('/getReservationDetailEquipment', [ReservationDetailEquipmentsController::class, 'getReservationDetailEquipment'])->name('ReservationDetailEquipments.getReservationDetailEquipment');
        Route::post('/setReservationDetailEquipment', [ReservationDetailEquipmentsController::class, 'setReservationDetailEquipment'])->name('ReservationDetailEquipments.setReservationDetailEquipment');
    });

    Route::prefix('ReservationStatuses')->group(function (){
        Route::post('/getReservationStatuses', [ReservationStatusesController::class, 'getReservationStatuses'])->name('ReservationStatuses.getReservationStatuses');
        Route::post('/getReservationStatus', [ReservationStatusesController::class, 'getReservationStatus'])->name('ReservationStatus.getReservationStatus');
        Route::post('/setReservationStatus', [ReservationStatusesController::class, 'setReservationStatus'])->name('ReservationStatus.setReservationStatus');
        Route::post('/setStatus', [ReservationStatusesController::class, 'setStatus'])->name('setReservationStatus.setStatus');
    });

    Route::prefix('ReservationTypes')->group(function (){
        Route::post('/getReservationTypes', [ReservationTypesController::class, 'getReservationTypes'])->name('ReservationTypes.getReservationTypes');
        Route::post('/getReservationType', [ReservationTypesController::class, 'getReservationType'])->name('ReservationType.getReservationType');
        Route::post('/setReservationType', [ReservationTypesController::class, 'setReservationType'])->name('ReservationType.setReservationType');
        Route::post('/setStatus', [ReservationTypesController::class, 'setStatus'])->name('ReservationType.setStatus');
    });

    Route::prefix('ReservationRefundTypes')->group(function (){
        Route::post('/getReservationRefundTypes', [ReservationRefundTypesController::class, 'getReservationRefundTypes'])->name('ReservationRefundTypes.getReservationRefundTypes');
        Route::post('/getReservationRefundType', [ReservationRefundTypesController::class, 'getReservationRefundType'])->name('ReservationRefundType.getReservationRefundType');
        Route::post('/setReservationRefundType', [ReservationRefundTypesController::class, 'setReservationRefundType'])->name('ReservationRefundTypes.setReservationRefundType');
        Route::post('/setStatus', [ReservationRefundTypesController::class, 'setStatus'])->name('ReservationRefundTypes.setStatus');
    });

    // U
    Route::prefix('Users')->group(function (){
        Route::post('/getUsers', [UsersController::class, 'getUsers'])->name('users.getUsers');
        Route::post('/getUser', [UsersController::class, 'getUser'])->name('users.getUser');
        Route::post('/getDetailsForUserCreations', [UsersController::class, 'getDetailsForUserCreations'])->name('users.getDetailsForUserCreations');
        Route::post('/getDetailsForUserEdit', [UsersController::class, 'getDetailsForUserEdit'])->name('users.getDetailsForUserEdit');
        Route::post('/setStatus', [UsersController::class, 'setStatus'])->name('users.setStatus');

    });


    Route::prefix('UserRoles')->group(function (){
        Route::post('/getUserRoles', [UserRolesController::class, 'getUserRoles'])->name('userRoles.getUserRoles');
        Route::post('/getUserRole', [UserRolesController::class, 'getUserRole'])->name('userRoles.getUserRole');
        Route::post('/setUserRole', [UserRolesController::class, 'setUserRole'])->name('userRoles.setUserRole');
        Route::post('/setStatus', [UserRolesController::class, 'setStatus'])->name('userRoles.setStatus');
    });

    Route::prefix('UserStatuses')->group(function (){
        Route::post('/getUserStatuses', [UserStatusesController::class, 'getUserStatuses'])->name('UserStatuses.getUserStatuses');
        Route::post('/getUserStatus', [UserStatusesController::class, 'getUserStatus'])->name('UserStatuses.getUserStatus');
        Route::post('/setStatus', [UserStatusesController::class, 'setStatus'])->name('UserStatuses.setStatus');
    });

    // v
    Route::prefix('VendorPayments')->group(function (){
        Route::post('/getVendorPayments', [VendorPaymentsController::class, 'getVendorPayments'])->name('VendorPayments.getVendorPayments');
        Route::post('/getVendorPayment', [VendorPaymentsController::class, 'getVendorPayment'])->name('VendorPayments.getVendorPayment');
        Route::post('/setVendorPayment', [VendorPaymentsController::class, 'setVendorPayment'])->name('VendorPayments.setVendorPayment');
    });
});
