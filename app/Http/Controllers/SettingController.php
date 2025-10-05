<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateAccountSettingRequest;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Services\SettingService;
use Inertia\Inertia;

class SettingController extends Controller
{
    private $settingService;

    public function __construct()
    {
        $this->settingService = new SettingService();
    }

    public function index()
    {
        $user = $this->settingService->getUser();
        $currencies = $this->settingService->getCurrencies();
        $secondaryCurrency = $this->settingService->getSecondaryCurrency();
        $theme = $this->settingService->getTheme();

        return Inertia::render('settings/Index', [
            'user'              => $user,
            'currencies'        => $currencies,
            'secondaryCurrency' => $secondaryCurrency,
            'theme'             => $theme,
        ]);
    }

    public function updateAccountSettings(UpdateAccountSettingRequest $request)
    {
        $user = $this->settingService->updateAccountSettings($request->validated());

        return back();
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->settingService->updatePassword($request->validated());

        return back();
    }
}
