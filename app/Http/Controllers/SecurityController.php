<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class SecurityController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return Inertia::render('securities/Index', [
        ]);
    }
}
