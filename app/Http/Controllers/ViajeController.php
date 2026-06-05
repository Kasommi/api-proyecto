<?php

namespace App\Http\Controllers;

use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViajeController extends Controller
{
    public function index(Request $request)
{
    $uid = $request->query('firebase_uid');

    $query = Viaje::query();

    if ($uid) {
        $query->where('firebase_uid', $uid);
    }

    return response()->json(
        $query->latest()->get()->map(fn ($viaje) => $this->formatViaje($viaje))
    );
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'fecha_visita' => 'nullable|date',
            'firebase_uid' => 'nullable|string|max:255',
            'firebase_email' => 'nullable|email|max:255',
            'nivel_educativo' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('viajes', 'public');
        }
        $data['nivel_educativo'] = $data['nivel_educativo'] ?? 'Superior';

        $viaje = Viaje::create($data);

        return response()->json([
            'message' => 'Viaje creado correctamente',
            'data' => $this->formatViaje($viaje),
        ], 201);
    }

    public function show(Viaje $viaje)
    {
        return response()->json([
            'data' => $this->formatViaje($viaje),
        ]);
    }

    public function update(Request $request, Viaje $viaje)
    {
        $data = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'fecha_visita' => 'nullable|date',
            'firebase_uid' => 'nullable|string|max:255',
            'firebase_email' => 'nullable|email|max:255',
            'nivel_educativo' => 'sometimes|nullable|string|max:255',
        ]);

        if ($request->hasFile('imagen')) {
            if ($viaje->imagen) {
                Storage::disk('public')->delete($viaje->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('viajes', 'public');
        }

        $viaje->update($data);

        return response()->json([
            'message' => 'Viaje actualizado correctamente',
            'data' => $this->formatViaje($viaje->fresh()),
        ]);
    }

    public function destroy(Viaje $viaje)
    {
        if ($viaje->imagen) {
            Storage::disk('public')->delete($viaje->imagen);
        }

        $viaje->delete();

        return response()->json([
            'message' => 'Viaje eliminado correctamente',
        ]);
    }

    private function formatViaje(Viaje $viaje): array
    {
        return [
            'id' => $viaje->id,
            'titulo' => $viaje->titulo,
            'descripcion' => $viaje->descripcion,
            'imagen' => $viaje->imagen,
            'imagen_url' => $viaje->imagen ? asset('storage/' . $viaje->imagen) : null,
            'latitud' => $viaje->latitud,
            'longitud' => $viaje->longitud,
            'fecha_visita' => $viaje->fecha_visita,
            'firebase_uid' => $viaje->firebase_uid,
            'firebase_email' => $viaje->firebase_email,
            'nivel_educativo' => $viaje->nivel_educativo,
            'created_at' => $viaje->created_at,
            'updated_at' => $viaje->updated_at,

        ];
    }
}