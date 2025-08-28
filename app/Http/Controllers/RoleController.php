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

class RoleController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        $limit = request()->input('limit', 10);
        $page  = request()->input('page', 1);

        $data = DB::table('roles')
            ->select('id', 'name', 'description', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
        $roleIds = $data->pluck('id')->toArray();

        $permissions = DB::table('permissions')
            ->join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
            ->whereIn('permission_role.role_id', $roleIds)
            ->select('permissions.id', 'permissions.name', 'permissions.slug', 'permissions.type', 'permission_role.role_id')
            ->get()
            ->groupBy('role_id');

        foreach ($data as $role) {
            $role->permissions = isset($permissions[$role->id]) ? $permissions[$role->id] : [];
        }
        // $permissions = DB::table('permissions')
        return $this->formatResponse(200, 'success', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required|unique:roles,name',
                'description' => 'nullable',
            ], [
                'name.required' => 'Nama role wajib diisi.',
                'name.unique' => 'Nama role sudah terdaftar.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            $role = DB::table('roles')->insertGetId([
                'name' => request('name'),
                'description' => request('description'),
                'created_at' => Carbon::now(),
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return $this->formatResponse(201, 'Role created successfully', ['id' => $role]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating role: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal server error', null);
        }
    }

    public function show(string $id)
    {
        $role = DB::table('roles')
            ->where('id', $id)
            ->select('id', 'name', 'description', 'created_at')
            ->first();
        $permissions = DB::table('permissions')
            ->join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
            ->where('permission_role.role_id', $id)
            ->select('permissions.id', 'permissions.name', 'permissions.slug', 'permissions.type')
            ->get();

        if (!$role) {
            return $this->formatResponse(404, 'Role not found', null);
        }
        $role->permissions = $permissions;

        return $this->formatResponse(200, 'success', $role);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles,name,' . $id,
                'description' => 'nullable',
            ], [
                'name.required' => 'Nama role wajib diisi.',
                'name.unique' => 'Nama role sudah terdaftar.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            DB::table('roles')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'updated_at' => Carbon::now(),
                    'updated_by' => Auth::id(),
                ]);

            DB::commit();
            return $this->formatResponse(200, 'Role updated successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating role: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal server error', null);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $role = DB::table('roles')->where('id', $id)->first();
            if (!$role) {
                return $this->formatResponse(404, 'Role not found', null);
            }

            DB::table('roles')->where('id', $id)->delete();
            DB::commit();
            return $this->formatResponse(200, 'Role deleted successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting role: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal server error', null);
        }
    }

    public function updatePermissions(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'permissions' => 'required',
            ], [
                'permissions.required' => 'Permissions wajib diisi.',
            ]);

            if ($validator->fails()) {
                $errorMessages = collect($validator->errors()->all())->implode(', ');
                return $this->formatResponse(422, $errorMessages, null);
            }

            // Clear existing permissions
            DB::table('permission_role')->where('role_id', $id)->delete();

            // Insert new permissions
            foreach ($request->permissions as $permission) {
                $permissionId = is_array($permission) ? $permission['id'] : $permission->id;
                DB::table('permission_role')->insert([
                    'role_id' => $id,
                    'permission_id' => $permissionId,
                    'created_at' => Carbon::now(),
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return $this->formatResponse(200, 'Permissions updated successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating permissions: ' . $e->getMessage());
            return $this->formatResponse(500, 'Internal server error', null);
        }
    }
}
