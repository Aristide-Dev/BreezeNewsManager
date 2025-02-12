<?php

namespace AristechDev\NewsManager\Http\Controllers\React;

use Inertia\Inertia;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        // $medias = Media::latest()->paginate(10);
        // $medias = Media::latest()->get();
        $categories = [
            "Événements officiels",
            "Réunions",
            "Conférences",
            "Formations",
            "Interviews",
            "Reportages",
            "Autres",
        ];
        return Inertia::render('Admin/Medias/Index', ['categories' =>$categories]);
    }

    public function create()
    {
        return Inertia::render('Admin/Medias/Create');
    }

    public function store(Request $request)
    {
        // Vérifier si au moins un champ est rempli
        if ($request->only(['title', 'description', 'category', 'url', 'file', 'embed_url', 'duration', 'published_at']) === []) {
            return back()->withErrors(['general' => 'Au moins un champ doit être rempli.']);
        }

        // Règles de validation de base
        $rules = [
            'type' => 'required|in:image,video',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'embed_url' => 'nullable|url',
            'duration' => 'nullable|integer',
            'published_at' => 'nullable|date',
        ];

        // Ajout des règles conditionnelles selon le mode d'upload
        if ($request->hasFile('file')) {
            $rules['file'] = 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mkv|max:10240';
        } elseif ($request->has('url')) {
            if (str_starts_with($request->url, 'http')) {
                $rules['url'] = 'required|url';
            } else {
                $rules['url'] = 'required|string';
            }
        }

        // Validation des données entrantes
        $validatedData = $request->validate($rules);

        try {
            // Si un fichier est téléchargé
            if ($request->hasFile('file')) {
                $validatedData['url'] = $request->file('file')->store('uploads/media', 'public');
            }

            // Créer le média
            Media::create($validatedData);

            return redirect()->route('admin.medias.index')
                ->with('success', 'Média créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.medias.index')
                ->with('error', 'Une erreur s\'est produite lors de la création du média : ' . $e->getMessage());
        }
    }

    public function edit(Media $media)
    {
        // dd($media);
        return Inertia::render('Admin/Medias/Create', ['media' => $media]);
    }

    public function update(Request $request, Media $media)
    {
        // Vérifier si au moins un champ est rempli
        if ($request->only(['title', 'description', 'category', 'url', 'file', 'embed_url', 'duration', 'published_at']) === []) {
            return back()->withErrors(['general' => 'Au moins un champ doit être rempli.']);
        }

        // Règles de validation de base
        $rules = [
            'type' => 'required|in:image,video',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'embed_url' => 'nullable|url',
            'duration' => 'nullable|integer',
            'published_at' => 'nullable|date',
        ];

        // Ajout des règles conditionnelles selon le mode d'upload
        if ($request->hasFile('file')) {
            $rules['file'] = 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mkv|max:10240';
        } elseif ($request->has('url')) {
            if (str_starts_with($request->url, 'http')) {
                $rules['url'] = 'required|url';
            } else {
                $rules['url'] = 'required|string';
            }
        }

        // Validation des données entrantes
        $validatedData = $request->validate($rules);

        try {
            // Vérifier si les données sont identiques aux données existantes
            $hasChanges = false;
            foreach ($validatedData as $key => $value) {
                if ($media->$key != $value) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges && !$request->hasFile('file')) {
                return back()->withErrors(['general' => 'Aucune modification n\'a été effectuée.']);
            }

            // Gestion du fichier téléchargé
            if ($request->hasFile('file')) {
                // Supprimer l'ancien fichier si nécessaire
                if ($media->url && !str_starts_with($media->url, 'http') && Storage::disk('public')->exists($media->url)) {
                    Storage::disk('public')->delete($media->url);
                }

                // Stocker le nouveau fichier
                $validatedData['url'] = $request->file('file')->store('uploads/media', 'public');
            }

            // Ne pas écraser l'URL existante si aucune nouvelle URL ou fichier n'est fourni
            if (!isset($validatedData['url']) && !$request->hasFile('file')) {
                unset($validatedData['url']);
            }

            // Mettre à jour les données du média
            $media->update($validatedData);

            return redirect()->route('admin.medias.index')
                ->with('success', 'Média mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.medias.index')
                ->with('error', 'Une erreur s\'est produite lors de la mise à jour du média : ' . $e->getMessage());
        }
    }


    public function destroy(Media $media)
    {
         // Supprimer le fichier si nécessaire
         if ($media->url && Storage::disk('public')->exists($media->url)) {
            Storage::disk('public')->delete($media->url);
        }

        $media->delete();
        return back()->with('success', 'Média supprimé avec succès.');
    }

    public function publicMedias()
    {
        $medias = Media::latest()->get();
        return Inertia::render('Public/Medias/Index', ['medias' => $medias]);
    }
}
