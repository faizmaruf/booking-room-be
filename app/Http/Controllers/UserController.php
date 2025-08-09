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


class UserController extends Controller
{
    use ResponseTrait;
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page  = $request->input('page', 1);

        $data = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('work_units', 'users.work_unit_id', '=', 'work_units.id')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.image_path', 'roles.id as role_id', 'roles.name as role_name', 'work_units.id as work_unit_id', 'work_units.name as work_unit_name', 'users.created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);


        return $this->formatResponse(200, 'success', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'    => 'required|unique:users,email',
                'phone'    => 'nullable',
                'image_path'    => 'nullable',
                'role_id'     => 'required',
                'password' => 'required|min:6',
                'work_unit_id' => 'nullable',
            ], [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi. berupa NIP',
                'password.required' => 'Password wajib diisi.',
                'password.min'      => 'Password minimal 6 karakter.',
                'email.unique'      => 'Email atau NIP sudah terdaftar.',
                'role_id.required'     => 'Role wajib diisi.',
            ]);
            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422,  $errorMessages, null);
            }
            if ($request->filled('image_path')) {
                $imageData = $request->image_path;

                // Parse base64 string
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $image = substr($imageData, strpos($imageData, ',') + 1);
                    $image = base64_decode($image);
                    $extension = strtolower($type[1]); // jpg, png, gif, etc.
                    $date = Carbon::now()->format('Y-m-d');
                    $filename = 'user_image_' . Auth::id() . '_' . uniqid() . '_' . $date . '.' . $extension;
                    $path = storage_path('app/public/user_images/' . $filename);
                    file_put_contents($path, $image);

                    // Store the filename or URL to DB
                    $image_url = 'user_images/' . $filename;
                } else {
                    return response()->json(['error' => 'Invalid image format'], 400);
                }
            }
            // Create directory if it doesn't exist
            if (!file_exists(storage_path('app/public/user_images'))) {
                mkdir(storage_path('app/public/user_images'), 0755, true);
            }



            $user = [
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'image_path' => $image_url ?? null,
                'role_id'      => $request->role_id,
                'work_unit_id' => $request->work_unit_id ?? null,
                'password'  => Hash::make($request->password),
                'created_at' => Carbon::now(),
                'created_by' => Auth::id() ?? null,
            ];

            DB::table('users')->insert($user);

            DB::commit();

            return $this->formatResponse(201, 'Data added successfully', $user);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }

    public function show(string $id)
    {
        $user = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('work_units', 'users.work_unit_id', '=', 'work_units.id')
            ->select('users.id', 'users.name', 'users.email', 'users.phone', 'users.image_path', 'roles.id as role_id', 'roles.name as role_name', 'work_units.id as work_unit_id', 'work_units.name as work_unit_name', 'users.created_at')
            ->where('users.id', $id)
            ->first();
        return $this->formatResponse(200, 'success', $user);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'nullable',
                'email'    => 'nullable',
                'phone'    => 'nullable',
                'image_path'    => 'nullable',
                'role_id'     => 'nullable',
                'password' => 'nullable',
                'work_unit_id' => 'nullable',
            ], []);
            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422,  $errorMessages, null);
            }
            if ($request->filled('image_path')) {
                $imageData = $request->image_path;

                // Parse base64 string
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $image = substr($imageData, strpos($imageData, ',') + 1);
                    $image = base64_decode($image);
                    $extension = strtolower($type[1]); // jpg, png, gif, etc.
                    $date = Carbon::now()->format('Y-m-d');
                    $filename = 'user_image_' . Auth::id() . '_' . uniqid() . '_' . $date . '.' . $extension;
                    $path = storage_path('app/public/user_images/' . $filename);
                    file_put_contents($path, $image);

                    // Store the filename or URL to DB
                    $image_url = 'user_images/' . $filename;
                } else {
                    return response()->json(['error' => 'Invalid image format'], 400);
                }
            }
            // Create directory if it doesn't exist
            if (!file_exists(storage_path('app/public/user_images'))) {
                mkdir(storage_path('app/public/user_images'), 0755, true);
            }

            $user = [
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'image_path' => $image_url ?? null,
                'role_id'      => $request->role_id,
                'work_unit_id' => $request->work_unit_id ?? null,
                'updated_at' => Carbon::now(),
                'updated_by' => Auth::id() ?? null,
            ];

            if ($request->filled('password')) {
                $user['password'] = Hash::make($request->password);
            }

            DB::table('users')
                ->where('id', $id)
                ->update($user);

            DB::commit();

            return $this->formatResponse(200, 'Data updated successfully', $user);
        } catch (\Exception $e) {
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
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return $this->formatResponse(404, 'User not found', null);
            }

            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return $this->formatResponse(200, 'User deleted successfully', null);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return $this->formatResponse(500, 'There is something wrong', null);
        }
    }
}
