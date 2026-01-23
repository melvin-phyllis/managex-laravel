<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeolocationZone;
use Illuminate\Http\Request;

class GeolocationZoneController extends Controller
{
    /**
     * Afficher la liste des zones
     */
    public function index()
    {
        $zones = GeolocationZone::withCount('presences')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.geolocation-zones.index', compact('zones'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.geolocation-zones.create');
    }

    /**
     * Enregistrer une nouvelle zone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:geolocation_zones,name',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        // Si c'est la zone par défaut, désactiver les autres
        if ($validated['is_default']) {
            GeolocationZone::where('is_default', true)->update(['is_default' => false]);
        }

        GeolocationZone::create($validated);

        return redirect()->route('admin.geolocation-zones.index')
            ->with('success', 'Zone de géolocalisation créée avec succès.');
    }

    /**
     * Afficher une zone
     */
    public function show(GeolocationZone $geolocationZone)
    {
        return view('admin.geolocation-zones.show', compact('geolocationZone'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(GeolocationZone $geolocationZone)
    {
        return view('admin.geolocation-zones.edit', compact('geolocationZone'));
    }

    /**
     * Mettre à jour une zone
     */
    public function update(Request $request, GeolocationZone $geolocationZone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:geolocation_zones,name,' . $geolocationZone->id,
            'description' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        // Si c'est la zone par défaut, désactiver les autres
        if ($validated['is_default']) {
            GeolocationZone::where('is_default', true)
                ->where('id', '!=', $geolocationZone->id)
                ->update(['is_default' => false]);
        }

        $geolocationZone->update($validated);

        return redirect()->route('admin.geolocation-zones.index')
            ->with('success', 'Zone de géolocalisation mise à jour avec succès.');
    }

    /**
     * Supprimer une zone
     */
    public function destroy(GeolocationZone $geolocationZone)
    {
        $geolocationZone->delete();

        return redirect()->route('admin.geolocation-zones.index')
            ->with('success', 'Zone de géolocalisation supprimée avec succès.');
    }

    /**
     * Définir comme zone par défaut
     */
    public function setDefault(GeolocationZone $geolocationZone)
    {
        GeolocationZone::where('is_default', true)->update(['is_default' => false]);
        $geolocationZone->update(['is_default' => true]);

        return redirect()->route('admin.geolocation-zones.index')
            ->with('success', 'Zone définie comme zone par défaut.');
    }
}
