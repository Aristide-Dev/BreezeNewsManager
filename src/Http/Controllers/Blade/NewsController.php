<?php

namespace AristechDev\NewsManager\Http\Controllers\Blade;

use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('published_at', 'desc')->paginate(10);
        return inertia('Admin/News/Index', ['news' => $news]);
    }

    public function create()
    {
        return inertia('Admin/News/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);
        // dd($request);

        if ($request->featured) {
            $featuredCount = News::where('featured', true)->count();
            if ($featuredCount >= 5) {
                return redirect()->back()->withErrors(['featured' => 'Vous ne pouvez pas avoir plus de 5 articles à la une.']);
            }
        }
        

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news_images','public');
        }

        $validated['tags'] = json_encode($validated['tags'] ?? []);

        $validated['slug'] = News::generateUniqueSlug($validated['title']);
        $validated['read_time'] = News::calculateReadingTime($validated['content']);

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'Actualité créée avec succès.');
    }

    public function edit(News $news)
    {
        // dd($news);
        $news->tags = json_decode($news->tags, true) ?? [];
        return inertia('Admin/News/Edit', ['news' => $news]);
    }

    public function update(Request $request, News $news)
    {
        // dd($request);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            // 'tags.*' => 'string|max:50',
            'featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($request->featured) {
            $featuredCount = News::where('featured', true)->count();
            if ($featuredCount >= 5) {
                return redirect()->back()->withErrors(['featured' => 'Vous ne pouvez pas avoir plus de 5 articles à la une.']);
            }
        }

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($news->image) {
                Storage::delete($news->image);
            }
            $validated['image'] = $request->file('image')->store('news_images', 'public');
        }else{
            unset($validated['image']);
        }

        $validated['featured'] = $validated['featured'] ? true:false;


        $validated['tags'] = json_encode($validated['tags'] ?? []);

        $validated['slug'] = News::generateUniqueSlug($validated['title'], $news->id);
        if ($news->content !== $validated['content']) {
            $validated['read_time'] = News::calculateReadingTime($validated['content']); // Recalcul si le contenu change
        }

        // dd($validated['read_time']);

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'Actualité mise à jour avec succès.');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Actualité supprimée avec succès.');
    }
}
