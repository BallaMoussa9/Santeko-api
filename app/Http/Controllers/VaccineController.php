<?php

namespace App\Http\Controllers;

use App\Models\Vaccine;
use App\Http\Requests\VaccineRequest;
use Illuminate\Http\JsonResponse;

class VaccineController extends Controller
{
    public function index(): JsonResponse
    {
        $vaccines = Vaccine::all();
        return response()->json($vaccines);
    }

    public function store(VaccineRequest $request): JsonResponse
    {
        $vaccine = Vaccine::create($request->validated());
        return response()->json($vaccine, 201);
    }

    public function show(Vaccine $vaccine): JsonResponse
    {
        return response()->json($vaccine);
    }

    public function update(VaccineRequest $request, Vaccine $vaccine): JsonResponse
    {
        $vaccine->update($request->validated());
        return response()->json($vaccine);
    }

    public function destroy(Vaccine $vaccine): JsonResponse
    {
        $vaccine->delete();
        return response()->json(null, 204);
    }
}
