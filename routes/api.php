<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkUnitController;
use Illuminate\Support\Facades\Auth;


// Default root response
Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
});

// Auth routes (no token required)
Route::post('login',  [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']); // optional

Route::prefix('rooms')->group(function () {
    Route::get('/',       [RoomController::class, 'index']);
});
// Protected routes with JWT middleware
Route::middleware('auth.jwt')->group(function () {
    Route::get('me',  [AuthController::class, 'me']);

    // Authenticated tests
    Route::get('auth-test', function () {
        return response()->json([
            'data' => Auth::user(),
            'message' => 'Authenticated successfully'
        ]);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/',       [UserController::class, 'index']);
        Route::post('/',      [UserController::class, 'store']);
        Route::get('{id}',    [UserController::class, 'show']);
        Route::put('{id}',    [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy']);
    });

    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('/',       [RoleController::class, 'index']);
        Route::post('/',      [RoleController::class, 'store']);
        Route::get('{id}',    [RoleController::class, 'show']);
        Route::put('{id}',    [RoleController::class, 'update']);
        Route::delete('{id}', [RoleController::class, 'destroy']);
        Route::prefix('{id}/role-permissions')->group(function () {
            Route::put('/',    [RoleController::class, 'updatePermissions']);
        });
    });

    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::get('/',       [PermissionController::class, 'index']);
        Route::post('/',      [PermissionController::class, 'store']);
        Route::get('{id}',    [PermissionController::class, 'show']);
        Route::put('{id}',    [PermissionController::class, 'update']);
        Route::delete('{id}', [PermissionController::class, 'destroy']);
    });

    // Rooms
    Route::prefix('rooms')->group(function () {
        Route::get('/',       [RoomController::class, 'index']);
        Route::post('/',      [RoomController::class, 'store']);
        Route::get('{id}',    [RoomController::class, 'show']);
        Route::put('{id}',    [RoomController::class, 'update']);
        Route::delete('{id}', [RoomController::class, 'destroy']);

        // Room Images nested
        Route::prefix('{room_id}/images')->group(function () {
            Route::post('/', [RoomController::class, 'storeImage']);
            Route::put('{image_id}', [RoomController::class, 'updateImage']);
            Route::delete('{image_id}', [RoomController::class, 'deleteImage']);
            Route::put('{image_id}/set-main', [RoomController::class, 'setMainImage']);
        });
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('get-booking-by-month-unit', [BookingController::class, 'getBookingByMonthUnit']);
        Route::get('get-booking-by-room', [BookingController::class, 'getBookingByRoom']);
        Route::get('/',       [BookingController::class, 'index']);
        Route::post('/',      [BookingController::class, 'store']);
        Route::get('{id}',    [BookingController::class, 'show']);
        Route::put('{id}',    [BookingController::class, 'update']);
        Route::delete('{id}', [BookingController::class, 'destroy']);
        Route::put('{id}/approve', [BookingController::class, 'approve']);
        Route::put('{id}/reject',  [BookingController::class, 'reject']);
    });

    // Room Images direct actions
    Route::prefix('room-images')->group(function () {
        Route::delete('{id}',     [RoomImageController::class, 'destroy']);
        Route::put('{id}/main',   [RoomImageController::class, 'setMain']);
    });

    Route::get('work-units', [WorkUnitController::class, 'index']);
    Route::post('work-units', [WorkUnitController::class, 'store']);
    Route::get('work-units/{id}', [WorkUnitController::class, 'show']);
    Route::put('work-units/{id}', [WorkUnitController::class, 'update']);
    Route::delete('work-units/{id}', [WorkUnitController::class, 'destroy']);
});
