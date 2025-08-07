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

class RoomController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $limit = request()->input('limit', 10);
        $page  = request()->input('page', 1);


        $rooms = DB::table('rooms')
            ->select('id', 'name', 'capacity', 'description', 'created_at')
            ->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);

        $roomIds = $rooms->pluck('id')->all();

        $roomImages = DB::table('room_images')
            ->select('id', 'room_id', 'image_path', 'is_main')
            ->whereIn('room_id', $roomIds)
            ->get()
            ->groupBy('room_id');

        foreach ($rooms as $room) {
            $room->images = $roomImages[$room->id] ?? collect();
        }

        return $this->formatResponse(200, 'success', $rooms);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required',
                'capacity'    => 'required|integer|min:1',
                'description' => 'nullable|string',
            ], [
                'name.required'        => 'Nama ruang wajib diisi.',
                'capacity.required'    => 'Kapasitas wajib diisi.',
                'capacity.integer'     => 'Kapasitas harus berupa angka.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }



            $room = DB::table('rooms')->insert([
                'name'        => $request->name,
                'capacity'    => $request->capacity,
                'description' => $request->description,
                'created_at'  => Carbon::now(),
                'created_by' => Auth::id(),
            ]);


            DB::commit();

            return $this->formatResponse(201, 'success', $room);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = DB::table('rooms')
            ->where('id', $id)->first();

        // Ambil semua gambar yang berkaitan
        $roomImages = DB::table('room_images')
            ->select('id', 'room_id', 'image_path', 'is_main')
            ->where('room_id', $id)
            ->get()
            ->groupBy('room_id');
        // Tambahkan gambar ke room
        if ($room) {
            $room->images = $roomImages[$id] ?? collect();
        }

        return $this->formatResponse(200, 'success', $room);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required',
                'capacity'    => 'required|integer|min:1',
                'description' => 'nullable|string',
            ], [
                'name.required'        => 'Nama ruang wajib diisi.',
                'capacity.required'    => 'Kapasitas wajib diisi.',
                'capacity.integer'     => 'Kapasitas harus berupa angka.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $room = DB::table('rooms')->where('id', $id)->update([
                'name'        => $request->name,
                'capacity'    => $request->capacity,
                'description' => $request->description,
                'updated_at'  => Carbon::now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(200, 'success', $room);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $room = DB::table('rooms')->where('id', $id)->first();
            if (!$room) {
                return $this->formatResponse(404, 'Room not found', null);
            }

            DB::table('rooms')->where('id', $id)->delete();

            DB::commit();

            return $this->formatResponse(200, 'Room deleted successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    public function storeImage(Request $request, string $room_id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'is_main' => 'boolean',
                'image' => 'required',
            ], [
                'image.required' => 'Image is required.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            if ($request->filled('image')) {
                $imageData = $request->image;

                // Parse base64 string
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $image = substr($imageData, strpos($imageData, ',') + 1);
                    $image = base64_decode($image);
                    $extension = strtolower($type[1]); // jpg, png, gif, etc.
                    $date = Carbon::now()->format('Y-m-d');
                    $filename = 'room_image_' . Auth::id() . '_' . $date . '.' . $extension;
                    $path = storage_path('app/public/room_images/' . $filename);
                    file_put_contents($path, $image);

                    // Store the filename or URL to DB
                    $image_url = 'room_images/' . $filename;
                } else {
                    return response()->json(['error' => 'Invalid image format'], 400);
                }
            }
            // Create directory if it doesn't exist
            if (!file_exists(storage_path('app/public/room_images'))) {
                mkdir(storage_path('app/public/room_images'), 0755, true);
            }

            // Store the image path in the database
            DB::table('room_images')->insert([
                'room_id'   => $room_id,
                'image_path' => $image_url ?? NULL,
                'is_main'   => $request->is_main ?? false,
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->formatResponse(201, 'Image uploaded successfully', [
                'image_path' => 'room_images/' . $filename,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    public function setMainImage(Request $request, string $room_id, string $image_id)
    {
        DB::beginTransaction();
        try {
            // Set all images for this room to not main
            DB::table('room_images')
                ->where('room_id', $room_id)
                ->update(['is_main' => false]);

            // Set the specified image as main
            DB::table('room_images')
                ->where('id', $image_id)
                ->where('room_id', $room_id)
                ->update(['is_main' => true]);

            DB::commit();

            return $this->formatResponse(200, 'Main image updated successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    public function deleteImage(string $room_id, string $image_id)
    {
        DB::beginTransaction();
        try {
            $image = DB::table('room_images')->where('id', $image_id)->where('room_id', $room_id)->first();
            if (!$image) {
                return $this->formatResponse(404, 'Image not found', null);
            }

            // Delete the image file from storage
            $filePath = storage_path('app/public/' . $image->image_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the image record from the database
            DB::table('room_images')->where('id', $image_id)->where('room_id', $room_id)->delete();

            DB::commit();

            return $this->formatResponse(200, 'Image deleted successfully', null);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }
}
