<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();

        return response()->json([
            'message' => 'Detail Data Role',
            'data' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        Role::create($request->all());

        return response()->json([
            "message" => "Tambah Role Berhasil"
            
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Detail Data Role",
            "data" => $role
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $role->name = $request['name'];

        $role->save();

        return response()->json([
            "message" => "Update Role berhasil",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                "message" => "id tidak ditemukan"
            ], 404);
        }

        $role->delete();

        return response()->json([
            "message" => "Berhasil Menghapus Role dengan id $id",
        ], 201);

    }
}
