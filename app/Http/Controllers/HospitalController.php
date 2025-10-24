<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Http\Requests\HospitalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HospitalController extends Controller
{
    /**
     * Affiche la liste des hôpitaux.
     */
    public function index(): JsonResponse
    {
        try {
            $hospitals = Hospital::all();
            Log::info('Liste des hôpitaux consultée par ' . (Auth::check() ? Auth::id() : 'Invité'));
            return response()->json($hospitals);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des hôpitaux: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des hôpitaux.'], 500);
        }
    }

    /**
     * Crée un nouvel hôpital.
     */
    public function store(HospitalRequest $request): JsonResponse
    {
        try {
            $hospital = Hospital::create($request->validated());
            Log::info('Hôpital créé par l\'utilisateur ' . Auth::id() . ': ' . $hospital->nom, ['hospital_id' => $hospital->id]);
            return response()->json($hospital, 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création d\'un hôpital: ' . $e->getMessage(), ['request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors de la création de l\'hôpital.'], 500);
        }
    }

    /**
     * Affiche un hôpital spécifique.
     */
    public function show(Hospital $hospital): JsonResponse
    {
        try {
            Log::info('Hôpital ' . $hospital->id . ' consulté par ' . (Auth::check() ? Auth::id() : 'Invité'));
            return response()->json($hospital);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la consultation de l\'hôpital ' . $hospital->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la consultation de l\'hôpital.'], 500);
        }
    }

    /**
     * Met à jour un hôpital existant.
     */
    public function update(HospitalRequest $request, Hospital $hospital): JsonResponse
    {
        try {
            $hospital->update($request->validated());
            Log::info('Hôpital ' . $hospital->id . ' mis à jour par l\'utilisateur ' . Auth::id(), ['new_data' => $hospital->toArray()]);
            return response()->json($hospital);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'hôpital ' . $hospital->id . ': ' . $e->getMessage(), ['request_data' => $request->all()]);
            return response()->json(['message' => 'Erreur lors de la mise à jour de l\'hôpital.'], 500);
        }
    }

    /**
     * Supprime un hôpital.
     */
    public function destroy(Hospital $hospital): JsonResponse
    {
        try {
            $hospital->delete();
            Log::info('Hôpital ' . $hospital->id . ' supprimé par l\'utilisateur ' . Auth::id());
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'hôpital ' . $hospital->id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suppression de l\'hôpital.'], 500);
        }
    }
}
