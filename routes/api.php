<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//A
use App\Http\Controllers\Services\ApplicationSettingsController;

//L
use App\Http\Controllers\Services\LocationsController;

//P
use App\Http\Controllers\Services\PaymentMethodsController;
use App\Http\Controllers\Services\PropertiesController;
use App\Http\Controllers\Services\PropertyRoomsController;
use App\Http\Controllers\Services\PropertyRoomEquipmentsController;
use App\Http\Controllers\Services\PropertyRoomFeaturesController;


//U
use App\Http\Controllers\Services\UsersController;
use App\Http\Controllers\Services\UserRolesController;
use App\Http\Controllers\Services\UserStatusesController;

//v
use App\Http\Controllers\Services\VendorPaymentsController;

Route::post('/Users/setUser', [UsersController::class, 'setUser'])->name('setUser');
Route::post('/Users/loginUser', [UsersController::class, 'loginUser'])->name('loginUser');

// A
Route::prefix('ApplicationSettings')->group(function (){
    Route::post('/getApplicationSetting', [ApplicationSettingsController::class, 'getApplicationSetting'])->name('ApplicationSettings.getApplicationSetting');
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


// Auth Routes
Route::middleware('auth:sanctum')->group(function () {

    // A
    Route::prefix('ApplicationSettings')->group(function (){
        Route::post('/setApplicationSetting', [ApplicationSettingsController::class, 'setApplicationSetting'])->name('ApplicationSettings.setApplicationSetting');
    });

    // L
    Route::prefix('Locations')->group(function (){
        Route::post('/setLocation', [LocationsController::class, 'setLocation'])->name('Locations.setLocation');
    });

    // P
    Route::prefix('Properties')->group(function (){
        Route::post('/setProperty', [PropertiesController::class, 'setProperty'])->name('Properties.setProperty');
    });


    Route::prefix('PropertyRooms')->group(function (){
        Route::post('/setPropertyRoom', [PropertyRoomsController::class, 'setPropertyRoom'])->name('propertyRooms.setPropertyRoom');
    });

    Route::prefix('PropertyRoomEquipments')->group(function (){
        Route::post('/setPropertyRoomEquipment', [PropertyRoomEquipmentsController::class, 'setPropertyRoomEquipment'])->name('propertyRoomEquipments.setPropertyRoomEquipment');
    });

    Route::prefix('PropertyRoomFeatures')->group(function (){
        Route::post('/setPropertyRoomFeature', [PropertyRoomFeaturesController::class, 'setPropertyRoomFeature'])->name('propertyRoomFeatures.setPropertyRoomFeature');
    });

    Route::prefix('PaymentMethods')->group(function (){
        Route::post('/getPaymentMethods', [PaymentMethodsController::class, 'getPaymentMethods'])->name('paymentMethods.getPaymentMethods');
        Route::post('/getPaymentMethod', [PaymentMethodsController::class, 'getPaymentMethod'])->name('paymentMethods.getPaymentMethod');
        Route::post('/setPaymentMethod', [PaymentMethodsController::class, 'setPaymentMethod'])->name('paymentMethods.setPaymentMethod');
    });

    // U
    Route::prefix('Users')->group(function (){
        Route::post('/getUsers', [UsersController::class, 'getUsers'])->name('Users.getUsers');
        Route::post('/getUser', [UsersController::class, 'getUser'])->name('Users.getUser');
        /*Route::post('/deleteUser', [UsersController::class, 'deleteUser'])->name('Users.deleteUser');
        Route::post('/activateUser', [UsersController::class, 'activateUser'])->name('Users.activateUser');
        Route::post('/logoutUser', [UsersController::class, 'logoutUser'])->name('Users.logoutUser');*/
    });

    Route::prefix('UserRoles')->group(function (){
        Route::post('/getUserRoles', [UserRolesController::class, 'getUserRoles'])->name('userRoles.getUserRoles');
        Route::post('/getUserRole', [UserRolesController::class, 'getUserRole'])->name('userRoles.getUserRole');
        Route::post('/setUserRole', [UserRolesController::class, 'setUserRole'])->name('userRoles.setUserRole');
    });

    Route::prefix('UserStatuses')->group(function (){
        Route::post('/getUserStatuses', [UserStatusesController::class, 'getUserStatuses'])->name('UserStatuses.getUserStatuses');
        Route::post('/getUserStatus', [UserStatusesController::class, 'getUserStatus'])->name('UserStatuses.getUserStatus');
        Route::post('/setUserStatus', [UserStatusesController::class, 'setUserStatus'])->name('UserStatuses.setUserStatus');
    });

    // v
    Route::prefix('VendorPayments')->group(function (){
        Route::post('/getVendorPayments', [VendorPaymentsController::class, 'getVendorPayments'])->name('VendorPayments.getVendorPayments');
        Route::post('/getVendorPayment', [VendorPaymentsController::class, 'getVendorPayment'])->name('VendorPayments.getVendorPayment');
        Route::post('/setVendorPayment', [VendorPaymentsController::class, 'setVendorPayment'])->name('VendorPayments.setVendorPayment');
    });
});
