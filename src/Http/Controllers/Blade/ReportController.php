<?php

namespace AristechDev\NewsManager\Http\Controllers\Blade;

use Inertia\Inertia;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->get();
        $categories = Report::CATEGORIES_LIST;
        return Inertia::render('Admin/Reports/Index', ['documents' => $reports, 'categories' => $categories]);
    }

    public function create()
    {
        $categories = Report::CATEGORIES_LIST;
        return Inertia::render('Admin/Reports/Create', ['categories' => $categories]);
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
    
        Report::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'tags' => $request->tags ? json_encode($request->tags) : null,
            'file_path' => $filePath,
            'published_at' => $request->published_at,
        ]);
    
        return redirect()->route('admin.reports.index')->with('success', 'Rapport créé avec succès.');
    }

    public function edit(Report $report)
    {
        $categories = Report::CATEGORIES_LIST;
        return Inertia::render('Admin/Reports/Create', ['report' => $report, 'categories' => $categories]);
    }

    public function update(Request $request, Report $report)
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
                if ($report->file_path && Storage::exists('reports/' . $report->file_path)) {
                    Storage::delete('reports/' . $report->file_path);
                }
    
                // Store the new file with a unique name
                $filePath = uniqid() . "_" . $request->file('file')->getClientOriginalName();
                $report->file_path = $request->file('file')->storeAs('reports', $filePath, 'public');
            }
    
            // Update the report
            $report->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'tags' => $request->tags ?? $report->tags, // Retain existing tags if not provided
                'file_path' => $report->file_path,
                'published_at' => $request->published_at,
            ]);
    
            // Redirect back with a success message
            return redirect()->route('admin.reports.index')->with('success', 'Rapport mis à jour avec succès.');
        } catch (\Exception $e) {
            // Handle any errors that occur during the process
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du rapport.')->withInput();
        }
    }

    public function destroy(Report $report)
    {
        Storage::delete($report->file_path);
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Rapport supprimé avec succès.');
    }

    public function publicDocuments()
    {
        return Inertia::render('Public/Documents/Index');
    }
}
