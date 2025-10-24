<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SystemSettingRequest; // On utilisera cette requête
use Illuminate\Http\Request; // N'est pas nécessaire si SystemSettingRequest est utilisé
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth; // Pour accéder à l'utilisateur authentifié
use Illuminate\Support\Facades\Gate; // Pour une autorisation plus structurée si nécessaire

class SystemSettingController extends Controller
{
    // public function __construct()
    // {
    //     // Applique le middleware d'authentification à toutes les méthodes du contrôleur,
    //     // sauf publicSettings qui peut être rendue publique via les routes.
    //     $this->middleware('auth:sanctum')->except(['publicSettings']);
    // }

    /**
     * Display a listing of the resource.
     * Seuls les admins voient tout.
     */
    public function index(): JsonResponse
    {
        // Vérification de l'autorisation : seul un admin peut lister tous les paramètres
        if (!Auth::user()->hasRole('admin')) { // Assurez-vous que hasRole fonctionne ou utilisez auth()->user()->role === 'admin'
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $settings = SystemSetting::all();
        return response()->json($settings);
    }

    /**
     * Store a newly created resource in storage.
     * Uniquement pour les administrateurs.
     */
    public function store(SystemSettingRequest $request): JsonResponse
    {
        // Vérification de l'autorisation : seul un admin peut créer des paramètres
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $data = $request->validated();
        $data['admin_id'] = Auth::id(); // Associe l'ID de l'administrateur créateur

        $setting = SystemSetting::create($data);
        return response()->json($setting, 201);
    }

    /**
     * Display the specified resource.
     * Les admins voient tout. Les autres peuvent voir si `is_public` est vrai.
     */
    public function show(SystemSetting $systemSetting): JsonResponse
    {
        // Vérification de l'autorisation :
        // Admin peut voir n'importe quel paramètre.
        // Les autres utilisateurs ne peuvent voir que les paramètres marqués comme public.
        if (Auth::user()->hasRole('admin') || ($systemSetting->is_public && Auth::check())) {
             // Si l'utilisateur est admin OU (si le paramètre est public ET un utilisateur est connecté)
             // L'Auth::check() est important pour les non-admins qui accèdent à un public paramètre
             // Si c'est pour tout le monde (même non-connecté), il faudrait ajuster ici et la route.
            return response()->json($systemSetting);
        }

        return response()->json(['message' => 'Accès non autorisé à ce paramètre.'], 403);
    }

    /**
     * Update the specified resource in storage.
     * Uniquement pour les administrateurs.
     */
    public function update(SystemSettingRequest $request, SystemSetting $systemSetting): JsonResponse
    {
        // Vérification de l'autorisation : seul un admin peut mettre à jour des paramètres
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        // Si vous voulez que certains paramètres ne soient pas éditables même par l'admin
        if (!$systemSetting->is_editable) {
            return response()->json(['message' => 'Ce paramètre système n\'est pas modifiable.'], 403);
        }

        $data = $request->validated();
        // admin_id n'est généralement pas mis à jour ici, mais si vous voulez tracer le dernier éditeur :
        // $data['admin_id'] = Auth::id();

        $systemSetting->update($data);
        return response()->json($systemSetting);
    }

    /**
     * Remove the specified resource from storage.
     * Uniquement pour les administrateurs.
     */
    public function destroy(SystemSetting $systemSetting): JsonResponse
    {
        // Vérification de l'autorisation : seul un admin peut supprimer des paramètres
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        // Si vous voulez que certains paramètres ne soient pas supprimables même par l'admin
        // Ex: if ($systemSetting->is_required) {
        //     return response()->json(['message' => 'Ce paramètre système est requis et ne peut être supprimé.'], 403);
        // }

        $systemSetting->delete();
        return response()->json(['message' => 'Paramètre système supprimé avec succès.'], 204); // 204 No Content est standard pour une suppression réussie sans corps de réponse
    }

    /**
     * Endpoint pour récupérer les paramètres publics sans authentification.
     * Cette méthode doit être routée sans middleware 'auth:sanctum'.
     */
    public function publicSettings(): JsonResponse
    {
        $publicSettings = SystemSetting::where('is_public', true)
                                       ->where('status', 'active') // Optionnel: ne renvoyer que les paramètres actifs
                                       ->get();
        return response()->json($publicSettings);
    }
}
