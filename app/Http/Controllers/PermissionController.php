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

class PermissionController extends Controller
{

    use ResponseTrait;
    public function index()
    {
        $limit = request()->input('limit', 10);
        $page  = request()->input('page', 1);

        $data = DB::table('permissions')
            ->select('id', 'name', 'slug', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return $this->formatResponse(200, 'success', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|unique:permissions,name',
                'slug' => 'required|unique:permissions,slug',
                'type' => 'required',
            ], [
                'name.required' => 'Nama permission wajib diisi.',
                'name.unique' => 'Nama permission sudah terdaftar.',
                'slug.required' => 'Slug permission wajib diisi.',
                'slug.unique' => 'Slug permission sudah terdaftar.',
                'type.required' => 'Tipe permission wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $permission = DB::table('permissions')->insertGetId([
                'name' => request('name'),
                'slug' => request('slug'),
                'type' => request('type'),
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return $this->formatResponse(201, 'Permission created successfully', ['id' => $permission]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating permission: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    public function show(string $id)
    {
        $permission = DB::table('permissions')
            ->where('id', $id)
            ->select('id', 'name', 'slug', 'type', 'created_at')
            ->first();

        if (!$permission) {
            return $this->formatResponse(404, 'Permission not found', null);
        }

        return $this->formatResponse(200, 'success', $permission);
    }


    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:permissions,name,' . $id,
                'slug' => 'required|unique:permissions,slug,' . $id,
                'type' => 'required',
            ], [
                'name.required' => 'Nama permission wajib diisi.',
                'name.unique' => 'Nama permission sudah terdaftar.',
                'slug.required' => 'Slug permission wajib diisi.',
                'slug.unique' => 'Slug permission sudah terdaftar.',
                'type.required' => 'Tipe permission wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            DB::table('permissions')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'type' => $request->type,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::commit();
            return $this->formatResponse(200, 'Permission updated successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating permission: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $permission = DB::table('permissions')->where('id', $id)->first();

            if (!$permission) {
                return $this->formatResponse(404, 'Permission not found', null);
            }

            DB::table('permissions')->where('id', $id)->delete();

            DB::commit();
            return $this->formatResponse(200, 'Permission deleted successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting permission: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal Server Error', null);
        }
    }
}
