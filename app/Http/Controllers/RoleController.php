<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    public function listRole(){
        return Role::all();
    }

   public function updateRole(RoleRequest $request, User $user): JsonResponse
{
    $role = Role::find($request->role_id);

    if (!$role) {
        return response()->json([
            'message' => "Rôle introuvable avec l'ID fourni."
        ], 404);
    }

    $departmentId = $request->department_id;

    DB::beginTransaction();

    try {
        if ($role->name === 'admin') {
            Admin::updateOrCreate(
                ['user_id' => $user->id],
                ['department_id' => $departmentId]
            );
        } else {
            Admin::where('user_id', $user->id)->delete();
        }

        // ✅ CORRIGÉ : Utiliser la méthode sync() sur la relation roles()
        $user->roles()->sync([$role->id]);

        DB::commit();

        return response()->json([
            'message' => "Le rôle de l'utilisateur a été mis à jour avec succès.",
            'user_roles' => $user->roles()->pluck('name'),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => "Erreur lors de la mise à jour du rôle : " . $e->getMessage(),
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function addRoleToUser(User $user, string $roleName, ?int $departmentId = null): JsonResponse
{
    DB::beginTransaction();

    try {
        $user->assignRole($roleName);

        if ($roleName === 'admin') {
            // Créer ou mettre à jour l'entrée 'Admin'
            Admin::updateOrCreate(
                ['user_id' => $user->id],
                ['department_id' => $departmentId] // Utiliser le departmentId passé en paramètre (peut être null)
            );
        }

        DB::commit();

        return response()->json([
            'message' => "Le rôle {$roleName} a été assigné à l'utilisateur.",
            'user_roles' => $user->roles()->pluck('name'),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => "Erreur lors de l'assignation du rôle : " . $e->getMessage(),
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function removeRoleFromUser(User $user, $roleName)
{
    $user->removeRole($roleName);
    return response()->json(['message' => "Role {$roleName} removed from user."]);
}

}
