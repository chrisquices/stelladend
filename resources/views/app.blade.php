<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $appearance ?? 'onyx' }}">
<head>
    <title inertia>{{ config('app.name', 'Stelladend') }} </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO -->
    <meta name="description" content="Stelladend is a modern dividend tracker that helps you monitor payouts, track dividend history, and stay ahead of the market.">
    <meta name="keywords" content="Stelladend, dividend tracker, dividend calendar, stock income, finance app, investing">
    <meta name="author" content="Stelladend">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Stelladend — Smart Dividend Tracker">
    <meta property="og:description" content="Track your dividends with precision. Monitor ex-div dates, yields, and payments across your portfolio.">
    <meta property="og:image" content="https://stelladend.com/social-banner.jpg">
    <meta property="og:url" content="https://stelladend.com">
    <meta property="og:site_name" content="Stelladend">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Stelladend — Smart Dividend Tracker">
    <meta name="twitter:description" content="Stay on top of dividend payouts with Stelladend — your reliable tool for income investors.">
    <meta name="twitter:image" content="https://stelladend.com/social-banner.jpg">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Stelladend" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Fonts & CSS -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://pro-cdn.lineicons.com/5.0/regular/lineicons.css">
    <link rel="stylesheet" href="https://pro-cdn.lineicons.com/5.0/solid/lineicons-solid.css">
    <link rel="stylesheet" href="https://pro-cdn.lineicons.com/4.0/light/lineicons-light.css"/>
    <link rel="stylesheet" href="https://pro-cdn.lineicons.com/4.0/fill/lineicons-fill.css"/>

    @routes
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased tracking-wide">

<!-- Background Effects -->
<div class="fixed inset-0 z-0 pointer-events-none overflow-hidden bg-gradient-to-br from-background-corner via-background to-background-corner">
    <!-- Rotating Stars -->
    @php
        $stars = [
            ['class' => 'animate-rotateStar', 'left' => '17%', 'top' => '82%', 'size' => 147, 'opacity' => 0.12],
            ['class' => 'animate-rotateStar', 'left' => '71%', 'top' => '67%', 'size' => 174, 'opacity' => 0.08],
            ['class' => 'animate-rotateStarTiny', 'left' => '73%', 'top' => '29%', 'size' => 131, 'opacity' => 0.17],
            ['class' => 'animate-rotateStar', 'left' => '22%', 'top' => '88%', 'size' => 180, 'opacity' => 0.07],
            ['class' => 'animate-rotateStarTiny', 'left' => '36%', 'top' => '19%', 'size' => 91, 'opacity' => 0.16],
            ['class' => 'animate-rotateStarTiny', 'left' => '8%', 'top' => '33%', 'size' => 168, 'opacity' => 0.28],
            ['class' => 'animate-rotateStarReverse', 'left' => '14%', 'top' => '56%', 'size' => 185, 'opacity' => 0.12],
            ['class' => 'animate-rotateStar', 'left' => '57%', 'top' => '87%', 'size' => 164, 'opacity' => 0.07],
            ['class' => 'animate-rotateStarTiny', 'left' => '90%', 'top' => '12%', 'size' => 108, 'opacity' => 0.10],
            ['class' => 'animate-rotateStar', 'left' => '53%', 'top' => '66%', 'size' => 184, 'opacity' => 0.10],
            ['class' => 'animate-rotateStarReverse', 'left' => '83%', 'top' => '66%', 'size' => 142, 'opacity' => 0.13],
            ['class' => 'animate-rotateStar', 'left' => '75%', 'top' => '14%', 'size' => 195, 'opacity' => 0.17],
            ['class' => 'animate-rotateStar', 'left' => '90%', 'top' => '13%', 'size' => 95, 'opacity' => 0.06],
            ['class' => 'animate-rotateStarReverse', 'left' => '51%', 'top' => '80%', 'size' => 126, 'opacity' => 0.26],
            ['class' => 'animate-rotateStarTiny', 'left' => '66%', 'top' => '43%', 'size' => 170, 'opacity' => 0.28],
            ['class' => 'animate-rotateStar', 'left' => '29%', 'top' => '73%', 'size' => 92, 'opacity' => 0.16],
            ['class' => 'animate-rotateStarReverse', 'left' => '91%', 'top' => '80%', 'size' => 162, 'opacity' => 0.28],
            ['class' => 'animate-rotateStarTiny', 'left' => '11%', 'top' => '93%', 'size' => 90, 'opacity' => 0.14],
        ];
    @endphp
    @foreach ($stars as $star)
        <div class="absolute {{ $star['class'] }}"
             style="left: {{ $star['left'] }}; top: {{ $star['top'] }}; width: {{ $star['size'] }}px; height: {{ $star['size'] }}px; opacity: {{ $star['opacity'] }};">
            <div class="absolute inset-0 w-0.5 h-full bg-gradient-to-b from-transparent via-accent to-transparent mx-auto"></div>
            <div class="absolute inset-0 h-0.5 w-full bg-gradient-to-r from-transparent via-accent to-transparent my-auto"></div>
        </div>
    @endforeach
</div>

<!-- Inertia -->
@inertia

<style>
    html { background-color: #0c0c1a; }
    html.dark { background-color: #0c0c1a; }

    @keyframes rotateStar { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes rotateStarSlow { 0% { transform: rotate(0deg); } 100% { transform: rotate(180deg); } }
    @keyframes rotateStarReverse { 0% { transform: rotate(360deg); } 100% { transform: rotate(0deg); } }
    @keyframes rotateStarTiny { 0% { transform: rotate(0deg); } 100% { transform: rotate(90deg); } }

    .animate-rotateStar { animation: rotateStar 20 linear infinite; }
    .animate-rotateStarSlow { animation: rotateStarSlow 30s linear infinite; }
    .animate-rotateStarReverse { animation: rotateStarReverse 45s linear infinite; }
    .animate-rotateStarTiny { animation: rotateStarTiny 60s linear infinite; }

    @keyframes driftSlow {
        0% { transform: translate(0px, 0px); }
        50% { transform: translate(8px, -6px); }
        100% { transform: translate(0px, 0px); }
    }
    @keyframes driftSmall {
        0% { transform: translate(0px, 0px); }
        50% { transform: translate(12px, -8px); }
        100% { transform: translate(0px, 0px); }
    }
    @keyframes driftSubtle {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.01); }
        100% { transform: translate(-50%, -50%) scale(1); }
    }

    .animate-driftSlow { animation: driftSlow 60s ease-in-out infinite; }
    .animate-driftSmall { animation: driftSmall 80s ease-in-out infinite; }
    .animate-driftSubtle { animation: driftSubtle 90s ease-in-out infinite; }
</style>

<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "Stelladend",
      "image": "https://stelladend.com/social-banner.jpg",
      "url": "https://stelladend.com",
      "author": {
        "@type": "Stelladend",
        "name": "Stelladend"
      },
      "description": "Modern dividend tracker with portfolio monitoring, stock analysis, calendar insights, and dual-currency support for long-term investors."
      "applicationCategory": "FinanceApplication",
      "operatingSystem": "Web",
      "offers": {
        "@type": "Offer",
        "price": "49",
        "priceCurrency": "USD"
      }
    }
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-WZFCLK8XF4"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-WZFCLK8XF4');
</script>

</body>
</html>
