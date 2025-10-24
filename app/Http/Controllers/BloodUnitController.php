<?php

namespace App\Http\Controllers;

use App\Models\BloodUnit;
use App\Http\Requests\BloodUnitRequest;
use Illuminate\Http\JsonResponse;

class BloodUnitController extends Controller
{
    public function index(): JsonResponse
    {
        // Chargez la relation 'donor' pour afficher les informations du donneur.
        $bloodUnits = BloodUnit::with('donor')->get();
        return response()->json($bloodUnits);
    }

    public function store(BloodUnitRequest $request): JsonResponse
    {
        // Le champ `donor_id` est géré automatiquement ici car il est inclus dans `$request->validated()`.
        $bloodUnit = BloodUnit::create($request->validated());
        return response()->json($bloodUnit, 201);
    }

    public function show(BloodUnit $bloodUnit): JsonResponse
    {
        // Chargez la relation 'donor' pour l'unité de sang spécifique.
        return response()->json($bloodUnit->load('donor'));
    }

    public function update(BloodUnitRequest $request, BloodUnit $bloodUnit): JsonResponse
    {
        $bloodUnit->update($request->validated());
        return response()->json($bloodUnit);
    }

    public function destroy(BloodUnit $bloodUnit): JsonResponse
    {
        $bloodUnit->delete();
        return response()->json(null, 204);
    }
}
