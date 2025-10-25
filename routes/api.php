<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//P
use App\Http\Controllers\Services\PropertiesController;
use App\Http\Controllers\Services\PropertyRoomsController;
use App\Http\Controllers\Services\PropertyRoomEquipmentsController;

//U
use App\Http\Controllers\Services\UsersController;



Route::post('/Users/setUser', [UsersController::class, 'setUser'])->name('setUser');
Route::post('/Users/loginUser', [UsersController::class, 'loginUser'])->name('loginUser');


// Auth Routes
Route::middleware('auth:sanctum')->group(function () {

    // P
    Route::prefix('Properties')->group(function (){
        Route::post('/getProperties', [PropertiesController::class, 'getProperties'])->name('Properties.getProperties');
        Route::post('/getProperty', [PropertiesController::class, 'getProperty'])->name('Properties.getProperty');
        Route::post('/setProperty', [PropertiesController::class, 'setProperty'])->name('Properties.setProperty');
    });


    Route::prefix('PropertyRooms')->group(function (){
        Route::post('/getPropertyRooms', [PropertyRoomsController::class, 'getPropertyRooms'])->name('propertyRooms.getPropertyRooms');
        Route::post('/getPropertyRoom', [PropertyRoomsController::class, 'getPropertyRoom'])->name('propertyRooms.getPropertyRoom');
        Route::post('/setPropertyRoom', [PropertyRoomsController::class, 'setPropertyRoom'])->name('propertyRooms.setPropertyRoom');
    });

    Route::prefix('PropertyRoomEquipments')->group(function (){
        Route::post('/getPropertyRoomEquipments', [PropertyRoomEquipmentsController::class, 'getPropertyRoomEquipments'])->name('propertyRoomEquipments.getPropertyRoomEquipments');
        Route::post('/getPropertyRoomEquipment', [PropertyRoomEquipmentsController::class, 'getPropertyRoomEquipment'])->name('propertyRoomEquipments.getPropertyRoomEquipment');
        Route::post('/setPropertyRoomEquipment', [PropertyRoomEquipmentsController::class, 'setPropertyRoomEquipment'])->name('propertyRoomEquipments.setPropertyRoomEquipment');
    });

    // U
    Route::prefix('Users')->group(function (){
        Route::post('/getUsers', [UsersController::class, 'getUsers'])->name('Users.getUsers');
        Route::post('/getUser', [UsersController::class, 'getUser'])->name('Users.getUser');
        /*Route::post('/deleteUser', [UsersController::class, 'deleteUser'])->name('Users.deleteUser');
        Route::post('/activateUser', [UsersController::class, 'activateUser'])->name('Users.activateUser');
        Route::post('/logoutUser', [UsersController::class, 'logoutUser'])->name('Users.logoutUser');*/
    });


});
