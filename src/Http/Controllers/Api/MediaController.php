<?php

namespace App\Http\Controllers\Api;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    /**
     * Récupérer les médias avec filtres (type, catégorie, recherche).
     */
    public function index(Request $request)
    {
        $query = Media::query();

        // Appliquer le filtre par type (image, video)
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Appliquer le filtre par catégorie
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Appliquer le filtre par recherche
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Charger les médias avec pagination
        $medias = $query->orderBy('created_at', 'desc')->get();

        return response()->json($medias);
    }
}
