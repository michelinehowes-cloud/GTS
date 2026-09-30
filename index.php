<?php

/**
 * Entry point fallback for environments where document root points to project root.
 */

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = urldecode(parse_url($rawUri, PHP_URL_PATH) ?? '/');

// Check if file exists in public/
$targetFile = null;
if ($uri !== '/' && is_file(__DIR__ . '/public' . $uri)) {
    $targetFile = __DIR__ . '/public' . $uri;
} elseif ($uri !== '/' && is_file(__DIR__ . $uri)) {
    $targetFile = __DIR__ . $uri;
}

if ($targetFile) {
    $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $mimes = [
        'css' => 'text/css; charset=UTF-8',
        'js'  => 'application/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg'=> 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'webp'=> 'image/webp',
        'woff'=> 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf' => 'font/ttf',
    ];

    header('Content-Type: ' . ($mimes[$ext] ?? mime_content_type($targetFile) ?: 'application/octet-stream'));
    header('Content-Length: ' . filesize($targetFile));
    header('Cache-Control: public, max-age=86400');
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
        readfile($targetFile);
    }
    exit;
}

require_once __DIR__ . '/public/index.php';
