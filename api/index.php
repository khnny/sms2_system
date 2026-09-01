<?php
/**
 * Vercel serverless entry point for SMS2.
 *
 * Routes incoming requests to the matching PHP file in the project root.
 * Set database env vars in the Vercel dashboard before expecting full app behavior.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);
$query = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY);
if (is_string($query) && $query !== '') {
    parse_str($query, $queryParams);
    foreach ($queryParams as $key => $value) {
        $_GET[$key] = $value;
    }
    $_SERVER['QUERY_STRING'] = $query;
}

if ($uri === '/api/index.php') {
    $uri = '/';
}

$candidates = [];
if ($uri === '/' || $uri === '') {
    $candidates[] = $root . '/index.php';
} else {
    $relative = ltrim($uri, '/');
    $candidates[] = $root . '/' . $relative;
    if (!str_ends_with(strtolower($relative), '.php')) {
        $candidates[] = $root . '/' . $relative . '.php';
        $candidates[] = $root . '/' . $relative . '/index.php';
    }
}

foreach ($candidates as $file) {
    $realRoot = realpath($root) ?: $root;
    $realFile = realpath($file);
    if ($realFile === false) {
        continue;
    }
    if (!str_starts_with($realFile, $realRoot)) {
        continue;
    }
    if (!is_file($realFile) || !str_ends_with(strtolower($realFile), '.php')) {
        continue;
    }

    require $realFile;
    return;
}

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo "SMS2: page not found on Vercel.\n";
echo "URI: {$uri}\n";
