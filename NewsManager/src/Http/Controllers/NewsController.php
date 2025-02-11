<?php

namespace AristechDev\NewsManager\Http\Controllers;

use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Liste des actualités']);
    }
} 