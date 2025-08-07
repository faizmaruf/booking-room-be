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
            ->select('id', 'name', 'email', 'phone', 'role', 'created_at')
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
                'role'     => 'nullable|in:admin,user',
                'password' => 'required|min:6',
            ], [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi. berupa NIP',
                'password.required' => 'Password wajib diisi.',
                'password.min'      => 'Password minimal 6 karakter.',
                'email.unique'      => 'Email atau NIP sudah terdaftar.',
                'role.in'          => 'Role harus berupa admin atau user.',
            ]);
            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422,  $errorMessages, null);
            }


            $user = [
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'role'      => $request->role,
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
            ->select('id', 'name', 'email', 'phone', 'role', 'created_at')
            ->where('id', $id)
            ->first();
        return $this->formatResponse(200, 'success', $user);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'    => 'required|unique:users,email,' . $id,
                'phone'    => 'nullable',
                'role'     => 'nullable',
                'password' => 'nullable|min:6',
            ], [
                'name.required'     => 'Nama wajib diisi.',
                'email.required'    => 'Email wajib diisi. berupa NIP',
                'email.unique'      => 'Email atau NIP sudah terdaftar.',
                'password.min'      => 'Password minimal 6 karakter.',
                'role.in'          => 'Role harus berupa admin atau user.',
            ]);
            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422,  $errorMessages, null);
            }

            $user = [
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'role'      => $request->role,
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
