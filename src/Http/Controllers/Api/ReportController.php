<?php

namespace App\Http\Controllers\Api;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Récupérer les rapports avec filtres (catégorie, recherche, pagination).
     */
    public function index(Request $request)
    {
        $query = Report::query();

        // Appliquer le filtre par catégorie
        if ($request->has('category') && $request->category !== 'Tous') {
            $query->where('category', $request->category);
        }

        // Appliquer le filtre par recherche
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Charger les rapports avec pagination
        $reports = $query->orderBy('published_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($reports);
    }

    /**
     * Télécharger un fichier de rapport.
     */
    public function download(Report $report)
    {
        if (!Storage::exists('reports/'.$report->file_path)) {
            return response()->json(['error' => 'Fichier introuvable.'], 404);
        }
        
        try {
            
            $pathToFile = storage_path('app/private/reports/' . $report->file_path);
            return response()->download($pathToFile);
            
        } catch (\Throwable $th) {
        //throw $th;
            return response()->json(['error' => 'Une Erreur est survenue. '.$th], 500);
        }

        
    }
}
