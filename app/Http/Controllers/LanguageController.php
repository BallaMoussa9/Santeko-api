<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Http\Requests\LanguageRequest;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Affiche la liste des langues.
     */
    public function index(): JsonResponse
    {
        $languages = Language::all();
        return response()->json($languages);
    }

    /**
     * Crée une nouvelle langue.
     */
    public function store(LanguageRequest $request): JsonResponse
    {
        $language = Language::create($request->validated());
        return response()->json($language, 201);
    }

    /**
     * Affiche une langue spécifique.
     */
    public function show(Language $language): JsonResponse
    {
        return response()->json($language);
    }

    /**
     * Met à jour une langue existante.
     */
    public function update(LanguageRequest $request, Language $language): JsonResponse
    {
        $language->update($request->validated());
        return response()->json($language);
    }

    /**
     * Supprime une langue.
     */
    public function destroy(Language $language): JsonResponse
    {
        $language->delete();
        return response()->json(['message' => 'Langue supprimée avec succès.'], 204);
    }
}
