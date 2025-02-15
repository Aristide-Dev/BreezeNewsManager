<?php

namespace AristechDev\NewsManager\Http\Controllers\React;

use Inertia\Inertia;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Document::latest()->get();
        $categories = Document::CATEGORIES_LIST;
        return Inertia::render('Admin/Documents/Index', ['documents' => $reports, 'categories' => $categories]);
    }

    public function create()
    {
        $categories = Document::CATEGORIES_LIST;
        return Inertia::render('Admin/Documents/Create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);
    
        $filePath = null;
    
        if ($request->hasFile('file')) {
            try {
                $originalFileName = $request->file->getClientOriginalName();
                $sanitizedFileName = str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9\._-]/', '', $originalFileName));
                $filePath = $request->file('file')->storeAs('reports/' . date('Y/m'), uniqid() . "_" . $sanitizedFileName, 'public');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Erreur lors du téléchargement du fichier.');
            }
        }
    
        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'tags' => $request->tags ? json_encode($request->tags) : null,
            'file_path' => $filePath,
            'published_at' => $request->published_at,
        ]);
    
        return redirect()->route('admin.documents.index')->with('success', 'Rapport créé avec succès.');
    }

    public function edit(Document $document)
    {
        $categories = Document::CATEGORIES_LIST;
        return Inertia::render('Admin/Documents/Create', ['report' => $document, 'categories' => $categories]);
    }

    public function update(Request $request, Document $document)
    {
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:20480',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);
    
        try {
            // Handle file upload if a new file is provided
            if ($request->hasFile('file')) {
                // Delete the old file if it exists
                if ($document->file_path && Storage::exists('reports/' . $document->file_path)) {
                    Storage::delete('reports/' . $document->file_path);
                }
    
                // Store the new file with a unique name
                $filePath = uniqid() . "_" . $request->file('file')->getClientOriginalName();
                $document->file_path = $request->file('file')->storeAs('reports', $filePath, 'public');
            }
    
            // Update the report
            $document->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'tags' => $request->tags ?? $document->tags, // Retain existing tags if not provided
                'file_path' => $document->file_path,
                'published_at' => $request->published_at,
            ]);
    
            // Redirect back with a success message
            return redirect()->route('admin.documents.index')->with('success', 'Rapport mis à jour avec succès.');
        } catch (\Exception $e) {
            // Handle any errors that occur during the process
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du rapport.')->withInput();
        }
    }

    public function destroy(Report $report)
    {
        Storage::delete($report->file_path);
        $report->delete();

        return redirect()->route('admin.documents.index')->with('success', 'Rapport supprimé avec succès.');
    }

    public function publicDocuments()
    {
        return Inertia::render('Public/Documents/Index');
    }
}
