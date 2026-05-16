<?php
$dirs = [
    'src/Layouts',
    'src/Views',
];

$baseCssDir = 'public/assets/css';
if (!is_dir($baseCssDir)) {
    mkdir($baseCssDir, 0777, true);
}

function processDir($dir, $baseCssDir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDir($path, $baseCssDir);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php' || pathinfo($path, PATHINFO_EXTENSION) === 'html') {
            processFile($path, $baseCssDir);
        }
    }
}

function processFile($path, $baseCssDir) {
    $content = file_get_contents($path);
    
    // We want to find <style>...</style>
    if (preg_match('/<style\b[^>]*>(.*?)<\/style>/is', $content, $match)) {
        $css = $match[1];
        
        // Determine file name
        $name = basename($path, '.php');
        $name = basename($name, '.html');
        $name = strtolower($name);
        
        // prefix with directory name if inside Views (like admin, company, vacancies)
        if (strpos($path, 'src/Views/admin/') !== false) {
            $name = 'admin_' . $name;
        } else if (strpos($path, 'src/Views/company/') !== false) {
            $name = 'company_' . $name;
        } else if (strpos($path, 'src/Views/vacancies/') !== false) {
            $name = 'vacancies_' . $name;
        } else if (strpos($path, 'src/Views/postulations/') !== false) {
            $name = 'postulations_' . $name;
        }

        // Special naming requested by user: navbar (from home.php ? Wait, navbar might be inside home.php, we'll separate manually if needed, but the user says "ejemplo home, admin, navbar, foter, siderbar, etc de cada uno")
        // If it's a layout, it's just header, footer, sidebar.
        
        $cssFile = $baseCssDir . '/' . $name . '.css';
        
        // append if exists? No, we just write. Wait, what if there are multiple <style> blocks in one file?
        // Let's use preg_replace_callback to replace all of them and concatenate.
        
        $allCss = '';
        $newContent = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function($m) use (&$allCss, $name) {
            $allCss .= $m[1] . "\n\n";
            return '<link rel="stylesheet" href="/assets/css/' . $name . '.css">';
        }, $content);
        
        file_put_contents($cssFile, trim($allCss));
        file_put_contents($path, $newContent);
        echo "Extracted $cssFile from $path\n";
    }
}

foreach ($dirs as $dir) {
    processDir($dir, $baseCssDir);
}
echo "Done.\n";
