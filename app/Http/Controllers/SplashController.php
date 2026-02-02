<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SplashController extends Controller
{
    // Marcar que el splash ya se mostró
    public function markAsShown(Request $request)
    {
        session()->put('splash_shown', true);
        
        return response()->json([
            'success' => true,
            'message' => 'Splash screen marked as shown'
        ]);
    }
    
    // Verificar si debe mostrarse el splash
    public function shouldShow()
    {
        $shouldShow = !session()->has('splash_shown');
        
        return response()->json([
            'should_show' => $shouldShow,
            'already_shown' => !$shouldShow
        ]);
    }
}