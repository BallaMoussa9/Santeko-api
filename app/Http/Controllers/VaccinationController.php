<?php

namespace App\Http\Controllers;

use App\Models\Vaccination;
use App\Http\Requests\VaccinationRequest;
use Illuminate\Http\JsonResponse;

class VaccinationController extends Controller
{
    public function index(): JsonResponse
    {
        $vaccinations = Vaccination::with(['vaccine', 'patient', 'nurse'])->get();
        return response()->json($vaccinations);
    }

    public function store(VaccinationRequest $request): JsonResponse
    {
        $vaccination = Vaccination::create($request->validated());
        return response()->json($vaccination, 201);
    }

    public function show(Vaccination $vaccination): JsonResponse
    {
        return response()->json($vaccination->load(['vaccine', 'patient', 'nurse']));
    }

    public function update(VaccinationRequest $request, Vaccination $vaccination): JsonResponse
    {
        $vaccination->update($request->validated());
        return response()->json($vaccination);
    }

    public function destroy(Vaccination $vaccination): JsonResponse
    {
        $vaccination->delete();
        return response()->json(null, 204);
    }
}
