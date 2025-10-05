<?php

namespace App\Http\Controllers;


use App\Http\Requests\Preferences\UpdatePreferenceRequest;
use App\Services\PreferenceService;

class PreferenceController extends Controller
{
    private $preferenceService;

    public function __construct() {
        $this->preferenceService = new PreferenceService();
    }

    public function update(UpdatePreferenceRequest $request) {
        $this->preferenceService->update($request->validated());
    }
}
