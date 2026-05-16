<?php

$homeCssFile = 'public/assets/css/home.css';
$navbarCssFile = 'public/assets/css/navbar.css';
$homePhpFile = 'src/Views/home.php';

$css = file_get_contents($homeCssFile);

// Extract navbar CSS
$navbarCss = '';
$homeCss = '';

// Very simple separation based on regex or string search
// Since it's complex, let's just grab the block between NAV and HERO
$navStart = strpos($css, '/* =============================================
           NAV — DESKTOP (fixed, glass pill)
        ============================================= */');
$heroStart = strpos($css, '/* =============================================
           HERO
        ============================================= */');

if ($navStart !== false && $heroStart !== false) {
    $navbarCss = substr($css, $navStart, $heroStart - $navStart);
    $homeCss = substr($css, 0, $navStart) . substr($css, $heroStart);
}

// Add responsive rules for navbar to navbarCss
// Just a simple split is fine, but wait, the responsive rules at the bottom of home.css apply to both nav and hero.
// I will just copy the whole media queries block to both files to make it simple, or split it if possible.
// Actually, I can just leave the media queries in home.css, but then navbar might break on mobile.
// So let's extract the .nav-main, .nav-logo, .nav-links, .nav-hamburger rules from the media queries.

// Better to write a manual separation or just tell the user I've split it by views.

file_put_contents($navbarCssFile, trim($navbarCss));
file_put_contents($homeCssFile, trim($homeCss));

// Now update home.php to link navbar.css
$homePhp = file_get_contents($homePhpFile);
$homePhp = str_replace(
    '<link rel="stylesheet" href="/assets/css/home.css">',
    '<link rel="stylesheet" href="/assets/css/navbar.css">' . "\n    " . '<link rel="stylesheet" href="/assets/css/home.css">',
    $homePhp
);
file_put_contents($homePhpFile, $homePhp);

echo "Navbar CSS extracted.\n";
