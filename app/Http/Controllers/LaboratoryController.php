<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Http\Requests\LaboratoryRequest;
use Illuminate\Http\JsonResponse;

class LaboratoryController extends Controller
{
    public function index(): JsonResponse
    {
        $laboratories = Laboratory::all();
        return response()->json($laboratories);
    }

    public function store(LaboratoryRequest $request): JsonResponse
    {
        $laboratory = Laboratory::create($request->validated());
        return response()->json($laboratory, 201);
    }

    public function show(Laboratory $laboratory): JsonResponse
    {
        return response()->json($laboratory);
    }

    public function update(LaboratoryRequest $request, Laboratory $laboratory): JsonResponse
    {
        $laboratory->update($request->validated());
        return response()->json($laboratory);
    }

    public function destroy(Laboratory $laboratory): JsonResponse
    {
        $laboratory->delete();
        return response()->json(null, 204);
    }
}
