<?php

declare(strict_types=1);

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }

    return match (strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'empty', '(empty)' => '',
        'null', '(null)' => null,
        default => $value,
    };
}

function base_path(string $path = ''): string
{
    $base = __DIR__ . '/..';
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function view_path(string $view): string
{
    $view = str_replace(['\\', '..'], ['/', ''], $view);
    $view = trim($view, '/');
    return base_path('resources/views/' . $view . '.php');
}

function route_url(string $page = 'dashboard'): string
{
    return '/index.php?page=' . urlencode($page);
}

function asset_url(string $asset): string
{
    return '/assets/' . ltrim($asset, '/');
}

function active_class(string $current, ?string $active): string
{
    return $current === $active ? 'active' : '';
}

function render(string $view, array $data = []): void
{
    $viewFile = view_path($view);
    if (!is_file($viewFile)) {
        http_response_code(500);
        throw new RuntimeException("View [{$view}] not found.");
    }

    $layout = $data['layout'] ?? 'layout/app';
    unset($data['layout']);
    $layoutFile = view_path($layout);
    if (!is_file($layoutFile)) {
        http_response_code(500);
        throw new RuntimeException("Layout [{$layout}] not found.");
    }

    $content = (static function (string $file, array $vars): string {
        extract($vars, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    })($viewFile, $data);

    extract($data, EXTR_SKIP);
    include $layoutFile;
}


