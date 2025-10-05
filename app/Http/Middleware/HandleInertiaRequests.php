<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name'        => config('app.name'),
            'auth'        => [
                'user' => $request->user()
                    ? [
                        'email'     => $request->user()->email,
                        'isPremium' => $request->user()->is_premium,
                    ]
                    : null,
            ],
            'ziggy'       => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
