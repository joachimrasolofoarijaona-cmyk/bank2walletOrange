<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentationController extends Controller
{
    /**
     * Affiche la documentation d'utilisation (visible par tous)
     */
    public function userGuide()
    {
        // Log de l'accès à la documentation utilisateur
        logActivity(
            session('username'),
            'documentation',
            'user_guide_access',
        );

        return view('documentation.userGuide');
    }

    /**
     * Affiche la documentation technique (réservée à INFORMATIQUE)
     */
    public function technicalGuide()
    {
        // Vérifier si l'utilisateur a la permission INFORMATIQUE
        $hasInformatiquePermission = false;
        foreach (session('selectedRoles') as $role) {
            if ($role['name'] === 'INFORMATIQUE' || $role['name'] === 'SUPER ADMIN') {
                $hasInformatiquePermission = true;
                break;
            }
        }

        if (!$hasInformatiquePermission) {
            return redirect()->back()->with('error', 'Accès non autorisé. Cette section est réservée au service informatique.');
        }

        // Log de l'accès à la documentation technique
        logActivity(
            session('username'),
            'documentation',
            'technical_guide_access',
        );

        return view('documentation.technicalGuide');
    }
}
