<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// Pastikan locale ke Indonesia
Carbon::setLocale('id');


class BookingController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $limit = request()->input('limit', 10);
        $page  = request()->input('page', 1);
        // variable bulan di tahun sekian 

        $user_id = request()->input('user_id');
        $query = DB::table('bookings as b')
            ->select(
                'b.*',
                'r.name as room_name',
                'ri.image_path as room_image',
                'u.name as user_name',
                'w.name as work_unit_name',
                'rejected_users.name as rejected_by_name',
                'approved_users.name as approved_by_name',
                'cancelled_users.name as cancelled_by_name'
            )
            ->join('rooms as r', 'b.room_id', '=', 'r.id')
            ->leftJoin('users as u', 'b.user_id', '=', 'u.id')
            ->leftJoin('work_units as w', 'u.work_unit_id', '=', 'w.id')
            ->leftJoin('room_images as ri', function ($join) {
                $join->on('r.id', '=', 'ri.room_id')
                    ->where('ri.is_main', 1);
            })
            ->leftJoin('users as rejected_users', 'b.rejected_by', '=', 'rejected_users.id')
            ->leftJoin('users as approved_users', 'b.approved_by', '=', 'approved_users.id')
            ->leftJoin('users as cancelled_users', 'b.deleted_by', '=', 'cancelled_users.id')
            ->where('b.deleted_at', null)
            ->orderBy('b.created_at', 'desc');


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
            $validator = Validator::make($request->all(), [

                'room_id'    => 'required',
                'booking_date' => 'required',
                'start_time' => 'required',
                'end_time'   => 'required|after:start_time',
                'purpose'    => 'nullable|string',
            ], [

                'room_id.required'    => 'Room ID wajib diisi.',
                'booking_date.required' => 'Booking date wajib diisi.',
                'start_time.required' => 'Start time wajib diisi.',
                'end_time.required'   => 'End time wajib diisi.',
                'end_time.after'      => 'End time harus setelah start time.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            // jika ada booking di hari dan jam dan room id nya sama, tolak
            $existingBooking = DB::table('bookings')
                ->where('room_id', $request->room_id)
                ->where('booking_date', $request->booking_date)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->whereNull('deleted_at')
                ->whereNull('rejected_by')
                ->first();
            if ($existingBooking) {
                return $this->formatResponse(409, 'Ruangan sudah terisi pada waktu tersebut', null);
            }
            $data = [
                'user_id'    => Auth::id(),
                'room_id'    => $request->room_id,
                'booking_date' => $request->booking_date ?? now()->toDateString(),
                'start_time' => $request->start_time,
                'purpose'    => $request->purpose,
                'end_time'   => $request->end_time,
                'status'     => 'pending', // Default status
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ];
            $this->sendNotification((object) $data);
            $booking = DB::table('bookings')->insert($data);

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

                'room_id'    => 'required',
                'booking_date' => 'required',
                'start_time' => 'required',
                'end_time'   => 'required|after:start_time',
            ], [

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
                'user_id'    => Auth::id(),
                'room_id'    => $request->room_id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'purpose'    => $request->purpose,
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

            DB::table('bookings')->where('id', $id)->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::id(),
                'status' => 'cancelled', // Assuming you want to mark it as cancelled
                'approved_at' => null,
                'approved_by' => null,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejected_reason' => null,
            ]);



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
                'approved_at' => Carbon::now(),
                'approved_by' => Auth::id(),
                'rejected_at' => null,
                'rejected_by' => null,
                'rejected_reason' => null,
                'deleted_at' => null,
                'deleted_by' => null
            ]);

            DB::commit();

            return $this->formatResponse(200, 'Booking approved successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    public function reject(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'reason'    => 'required',
            ], [
                'reason.required'    => 'Alasan reject wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $booking = DB::table('bookings')->where('id', $id)->first();

            if (!$booking) {
                return $this->formatResponse(404, 'Booking not found', null);
            }

            DB::table('bookings')->where('id', $id)->update([
                'status' => 'rejected',
                'updated_at' => null,
                'rejected_at' => Carbon::now(),
                'rejected_by' => Auth::id(),
                'rejected_reason' => $request->reason, // Optional reason for rejection
                'approved_at' => null,
                'approved_by' => null,
                'deleted_at' => null,
                'deleted_by' => null,

            ]);

            DB::commit();

            return $this->formatResponse(200, 'Booking rejected successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    // membuat function untuk get data bookiing by month unit terbanyak booking
    public function getBookingByMonthUnit(Request $request)
    {
        $dateInput = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($dateInput);

        $year  = $date->year;
        $month = $date->month;

        $bookings = DB::table('work_units')
            ->select('work_units.name as unit_name', DB::raw('COUNT(bookings.id) as total_bookings'))
            ->leftJoin('users', 'users.work_unit_id', '=', 'work_units.id')
            ->leftJoin('bookings', function ($join) use ($year, $month) {
                $join->on('bookings.user_id', '=', 'users.id')
                    ->whereYear('bookings.created_at', $year)
                    ->whereMonth('bookings.created_at', $month);
            })
            ->groupBy('work_units.name')
            ->orderByDesc('total_bookings')
            ->get();



        return $this->formatResponse(200, 'Data booking by month unit', $bookings);
    }
    // membuat function untuk get data booking by aula yang sering digunakan
    public function getBookingByRoom(Request $request)
    {
        $dateInput = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($dateInput);

        $year  = $date->year;
        $month = $date->month;

        $bookings = DB::table('rooms')
            ->select('rooms.name as room_name', DB::raw('COUNT(bookings.id) as total_bookings'))
            ->leftJoin('bookings', function ($join) use ($year, $month) {
                $join->on('bookings.room_id', '=', 'rooms.id')
                    ->whereYear('bookings.created_at', $year)
                    ->whereMonth('bookings.created_at', $month);
            })
            ->groupBy('rooms.name')
            ->orderByDesc('total_bookings')
            ->get();

        return $this->formatResponse(200, 'Data booking by room', $bookings);
    }
    private function sendNotification($booking)
    {

        $roomData = DB::table('rooms')
            ->select('rooms.name as room_name', 'users.name as pic_name', 'users.phone as pic_phone')
            ->leftJoin('users', 'rooms.pic_id', '=', 'users.id')
            ->where('rooms.id', $booking->room_id)
            ->first();

        $userData = DB::table('users')
            ->select('users.name as user_name', 'users.phone as user_phone', 'work_units.name as work_unit_name')
            ->leftJoin('work_units', 'users.work_unit_id', '=', 'work_units.id')
            ->where('users.id', $booking->user_id)
            ->first();


        $startDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
        $endDateTime   = Carbon::parse($booking->booking_date . ' ' . $booking->end_time);

        $tanggal = $startDateTime->translatedFormat('l, d F Y');
        $jamMulai = $startDateTime->format('H:i');
        $jamSelesai = $endDateTime->format('H:i');

        $message = "Halo {$roomData->pic_name}\n, ada booking baru:\n\n\n"
            . "{$roomData->room_name}\n\n"
            . "{$tanggal} ({$jamMulai} - {$jamSelesai}) \n\n"
            . "Untuk : {$booking->purpose}\n\n"
            . "Pemesan: {$userData->user_name}\n\n"
            . "Unit : {$userData->work_unit_name}\n\n";


        \App\Jobs\SendWhatsappNotificationJob::dispatch($roomData->pic_phone, $message);

        return back()->with('success', 'Booking tersimpan dan WA notif akan dikirim ke PIC.');
    }
}
