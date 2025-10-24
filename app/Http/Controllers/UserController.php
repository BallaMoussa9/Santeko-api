<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
//use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;


class UserController extends Controller
{
    // public function __construct()
    // {
    //     // Assure que l'utilisateur est authentifié pour toutes les actions de ce contrôleur
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * Récupère toutes les notifications de l'utilisateur.
     * @return JsonResponse
     */
    public function notifications(): JsonResponse
    {
        return response()->json(auth()->user()->notifications()->latest()->get());
    }

    /**
     * Récupère uniquement les notifications non lues.
     * @return JsonResponse
     */
    public function unreadNotifications(): JsonResponse
    {
        return response()->json(auth()->user()->unreadNotifications()->latest()->get());
    }

    /**
     * Marque une notification spécifique comme lue.
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marquée comme lue.']);
        }

        return response()->json(['message' => 'Notification non trouvée.'], 404);
    }

    /**
     * Marque toutes les notifications comme lues.
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Récupère tous les utilisateurs avec leurs rôles et départements,
     * pour l'affichage dans des listes simples ou des interfaces de gestion.
     * @return JsonResponse
     */
    public function getAllUsers(): JsonResponse
    {
        // if (!Auth::user()->hasRole('admin')) {
        //     return response()->json(['message' => 'Accès non autorisé. Seuls les administrateurs peuvent accéder à cette ressource.'], 403);
        // }

        $users = User::with(['roles', 'department'])
                     ->get()
                     ->map(function($user) {
                         // Ajoute l'URL de la photo de profil pour l'affichage frontend
                         $user->profile_photo_url = $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://via.placeholder.com/40';
                         return $user;
                     });

        return response()->json($users);
    }

    /**

     * Récupère les informations de l'utilisateur actuellement authentifié.

     * Cette fonction ne retourne QUE les informations de l'utilisateur connecté.

     * Elle est sécurisée par le middleware 'auth:sanctum'.

     *

     * @param Request $request

     * @return JsonResponse

     */

    public function fetchCurrentUser(Request $request): JsonResponse

    {


        $user = $request->user();



        if (!$user) {

            return response()->json(['message' => 'Non authentifié.'], 401);

        }


        // $user->load(['roles', 'department']);
        return response()->json($user);

    }
    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Recherche les utilisateurs avec des filtres et une pagination.
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request): JsonResponse
    {
        // if (!Auth::user()->hasRole('admin')) {
        //     return response()->json(['message' => 'Accès non autorisé. Seuls les administrateurs peuvent rechercher des utilisateurs.'], 403);
        // }

        $query = User::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('department_id') && $request->input('department_id') !== '') {
            $query->where('department_id', $request->input('department_id'));
        }

        $query->with(['roles', 'department']);

        $users = $query->paginate($request->input('per_page', 15));

        return response()->json($users);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Supprime un utilisateur.
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        // if (!Auth::user()->hasRole('admin')) {
        //     return response()->json(['message' => 'Accès non autorisé. Seuls les administrateurs peuvent supprimer des utilisateurs.'], 403);
        // }

        if (Auth::id() === $user->id) {
            return response()->json(['message' => 'Un administrateur ne peut pas supprimer son propre compte.'], 403);
        }

        DB::beginTransaction();
        try {
            // Supprime l'entrée Admin si l'utilisateur a le rôle d'admin
            if ($user->hasRole('admin')) {
                Admin::where('user_id', $user->id)->delete();
            }

            $user->delete();

            DB::commit();
            return response()->json(['message' => 'Utilisateur supprimé avec succès.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => "Erreur lors de la suppression de l'utilisateur.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Met à jour la photo de profil de l'utilisateur authentifié.
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'profile_photo' => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation lors du téléchargement de la photo.', 'errors' => $validator->errors()], 422);
        }

        $photo = $request->file('profile_photo');
        $path = $photo->store('profile-photos', 'public');

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->forceFill(['profile_photo' => $path])->save();
        $user->refresh(); // Recharge l'utilisateur pour mettre à jour les accesseurs

        return response()->json([
            'message' => 'Photo de profil mise à jour avec succès.',
            'profile_photo_url' => $user->profile_photo_url,
        ]);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Met à jour le rôle d'un utilisateur spécifique.
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
   // Exemple dans votre UserController, à l'intérieur d'un bloc try-catch par exemple

// ...
      /**
     * Met à jour le rôle (unique) d'un utilisateur spécifique.
     * Étant donné la méthode sync, l'utilisateur n'aura qu'un seul rôle après cette opération.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateUserRole(Request $request, User $user): JsonResponse
    {
        // 1. Vérification de l'autorisation : Seul un administrateur peut modifier les rôles.
        if (!Auth::user()->hasRole('admin')) {
            Log::warning("Tentative non autorisée de modification de rôle par l'utilisateur " . Auth::id());
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        // 2. Validation de l'entrée : Assure que le rôle est requis, de type string et existe dans la table 'roles'.
        $validator = Validator::make($request->all(), [
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        if ($validator->fails()) {
            Log::info("Validation échouée lors de la mise à jour du rôle de l'utilisateur {$user->id}.", [
                'errors' => $validator->errors()->toArray(),
                'requested_role' => $request->input('role')
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction(); // Démarre une transaction pour garantir l'atomicité de l'opération
        try {
            // 3. Trouver le rôle par son nom.
            //    firstOrFail() lèvera une ModelNotFoundException si le rôle n'existe pas,
            //    mais la validation Rule::exists() devrait déjà avoir empêché cela.
            $newRole = Role::where('name', $request->input('role'))->firstOrFail();

            // 4. Récupérer les rôles actuels de l'utilisateur pour le log (avant le changement).
            $currentRoleNames = $user->roles->pluck('name')->implode(', ');

            // 5. Synchroniser les rôles de l'utilisateur :
            //    Détache tous les rôles actuels et attache uniquement le nouveau rôle.
            $user->roles()->sync([$newRole->id]);

            // 6. Commettre la transaction si toutes les opérations sont réussies.
            DB::commit();

            // 7. Loguer le succès de l'opération.
            Log::info("Rôle de l'utilisateur {$user->id} mis à jour avec succès.", [
                'user_id' => $user->id,
                'old_roles' => $currentRoleNames,
                'new_role' => $newRole->name,
                'requested_by_admin' => Auth::id(),
            ]);

            // 8. Retourner une réponse JSON avec l'utilisateur mis à jour (incluant ses nouveaux rôles).
            return response()->json([
                'message' => "Rôle de l'utilisateur mis à jour avec succès.",
                'user' => $user->load('roles') // Recharge l'utilisateur et sa relation 'roles' pour la réponse
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack(); // Annule la transaction si le modèle (rôle) n'est pas trouvé
            Log::error("Le rôle demandé '{$request->input('role')}' n'a pas été trouvé lors de la mise à jour de l'utilisateur {$user->id}.", [
                'user_id' => $user->id,
                'requested_role' => $request->input('role'),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
            ]);
            return response()->json([
                'message' => "Le rôle spécifié n'existe pas.",
                'error' => $e->getMessage()
            ], 404); // Code 404 car la ressource (le rôle) n'a pas été trouvée
        } catch (\Exception $e) {
            DB::rollBack(); // Annule la transaction pour toute autre exception
            Log::error("Erreur inattendue lors de la mise à jour du rôle de l'utilisateur {$user->id} : " . $e->getMessage(), [
                'user_id' => $user->id,
                'requested_role' => $request->input('role'),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => "Erreur interne du serveur lors de la mise à jour du rôle.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

// ...

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Récupère la liste de tous les rôles disponibles.
     * @return JsonResponse
     */
    public function getAvailableRoles(): JsonResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $roles = Role::all(['name']);
        return response()->json($roles->pluck('name'));
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Récupère la liste de tous les départements.
     * @return JsonResponse
     */
    public function getDepartments(): JsonResponse
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        $departments = Department::all(['id', 'name']);
        return response()->json($departments);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Met à jour le profil de l'utilisateur authentifié (informations de base).
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfileInformation(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        $user->forceFill([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
        ])->save();

        return response()->json(['message' => 'Profil mis à jour avec succès.', 'user' => $user->refresh()]);
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Met à jour le mot de passe de l'utilisateur authentifié.
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 400);
        }

        $user->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }
}
