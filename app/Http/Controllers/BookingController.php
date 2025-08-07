<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class BookingController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $limit = request()->input('limit', 10);
        $page  = request()->input('page', 1);
        $user_id = request()->input('user_id');

        $query = DB::table('bookings')
            ->select('id', 'user_id', 'room_id', 'booking_date', 'start_time', 'end_time', 'status', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($user_id) {
            $query->where('user_id', $user_id);
        }
        $data = $query->paginate($limit, ['*'], 'page', $page);
        return $this->formatResponse(200, 'success', $data);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'user_id'    => 'required',
                'room_id'    => 'required',
                'booking_date' => 'required',
                'start_time' => 'required',
                'end_time'   => 'required|after:start_time',
            ], [
                'user_id.required'    => 'User ID wajib diisi.',
                'room_id.required'    => 'Room ID wajib diisi.',
                'booking_date.required' => 'Booking date wajib diisi.',
                'start_time.required' => 'Start time wajib diisi.',
                'end_time.required'   => 'End time wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $booking = DB::table('bookings')->insert([
                'user_id'    => Auth::id(),
                'room_id'    => $request->room_id,
                'booking_date' => $request->booking_date ?? now()->toDateString(),
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'status'     => 'pending', // Default status
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(201, 'Booking created successfully', $booking);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $booking = DB::table('bookings')->where('id', $id)->first();


        return $this->formatResponse(200, 'success', $booking);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'user_id'    => 'required',
                'room_id'    => 'required',
                'booking_date' => 'required',
                'start_time' => 'required',
                'end_time'   => 'required|after:start_time',
            ], [
                'user_id.required'    => 'User ID wajib diisi.',
                'room_id.required'    => 'Room ID wajib diisi.',
                'booking_date.required' => 'Booking date wajib diisi.',
                'start_time.required' => 'Start time wajib diisi.',
                'end_time.required'   => 'End time wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $booking = DB::table('bookings')->where('id', $id)->update([
                'user_id'    => $request->user_id,
                'room_id'    => $request->room_id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'status'     => $request->status ?? 'pending',
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(200, 'Booking updated successfully', $booking);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $booking = DB::table('bookings')->where('id', $id)->first();

            if (!$booking) {
                return $this->formatResponse(404, 'Booking not found', null);
            }

            DB::table('bookings')->where('id', $id)->delete();

            DB::commit();

            return $this->formatResponse(200, 'Booking deleted successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }
    public function approve(string $id)
    {
        DB::beginTransaction();
        try {
            $booking = DB::table('bookings')->where('id', $id)->first();

            if (!$booking) {
                return $this->formatResponse(404, 'Booking not found', null);
            }

            DB::table('bookings')->where('id', $id)->update([
                'status' => 'approved',
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(200, 'Booking approved successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    public function reject(string $id)
    {
        DB::beginTransaction();
        try {
            $booking = DB::table('bookings')->where('id', $id)->first();

            if (!$booking) {
                return $this->formatResponse(404, 'Booking not found', null);
            }

            DB::table('bookings')->where('id', $id)->update([
                'status' => 'rejected',
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(200, 'Booking rejected successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }
}
