<?php
/**
 * tinytorch/file-list.php
 * 
 * Proxies the tito API module list so that index.php's JS fetch()
 * call to 'tinytorch/file-list.php' gets a JSON array of module names.
 *
 * Also supports a direct filesystem scan as fallback if the API is down.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$TITO_API = 'https://my-examples-of-tito.onrender.com';

// ── 1. Try the live tito API ──────────────────────────────────────────────
$modules = [];
$apiUrl  = $TITO_API . '/api/v1/module?operation=list';

$ctx = stream_context_create([
    'http' => [
        'timeout'        => 8,
        'ignore_errors'  => true,
    ]
]);

$raw = @file_get_contents($apiUrl, false, $ctx);

if ($raw !== false) {
    $data = json_decode($raw, true);

    if (is_array($data)) {
        // Response is already an array
        $modules = $data;
    } elseif (isset($data['modules']) && is_array($data['modules'])) {
        $modules = $data['modules'];
    } elseif (isset($data['data']) && is_array($data['data'])) {
        $modules = $data['data'];
    } elseif (isset($data['output']) && is_string($data['output'])) {
        // Text output — split on newlines
        $modules = array_values(array_filter(
            array_map('trim', explode("\n", $data['output']))
        ));
    }
}

// ── 2. Fallback: scan local filesystem ───────────────────────────────────
if (empty($modules)) {
    $scanDirs = [
        __DIR__ . '/',                    // /app/tinytorch/
        __DIR__ . '/../',                 // /app/
        __DIR__ . '/src/',
        __DIR__ . '/modules/',
    ];

    foreach ($scanDirs as $dir) {
        if (!is_dir($dir)) continue;
        $files = glob($dir . '*.py') ?: [];
        $files = array_merge($files, glob($dir . '*.ipynb') ?: []);
        foreach ($files as $f) {
            $name = basename($f);
            // Skip internal / test files
            if (strpos($name, '__') === 0 || strpos($name, 'test_') === 0) continue;
            $modules[] = $name;
        }
        if (!empty($modules)) break;
    }

    sort($modules);
    $modules = array_unique($modules);
}

// ── 3. Last resort: return the known static module list ──────────────────
if (empty($modules)) {
    $modules = [
        '01_tensor',
        '02_autograd',
        '03_nn',
        '04_optim',
        '05_data',
        '06_training',
    ];
}

echo json_encode(array_values($modules));
