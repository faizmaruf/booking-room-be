<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Auth;


// Default root response
Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
});

// Auth routes (no token required)
Route::post('login',  [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']); // optional

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
    Route::prefix('bookings')->group(function () {
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
});
