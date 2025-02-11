<?php

namespace AristechDev\NewsManager\Http\Controllers;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Liste des médias']);
    }
} 