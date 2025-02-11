<?php

namespace AristechDev\NewsManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsManagerController extends Controller
{
    /**
     * Affiche la page d'accueil du gestionnaire d'actualités.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Vous pouvez ici retourner une vue ou une réponse JSON
        // Exemple avec une vue publiée par le package :
        // return view('newsmanager::welcome');

        return response()->json([
            'message' => "Bienvenue dans le package NewsManager"
        ]);
    }
} 