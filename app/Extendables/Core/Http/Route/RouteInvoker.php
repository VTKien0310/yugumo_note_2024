<?php

namespace App\Extendables\Core\Http\Route;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteInvoker
{
    public static function invokeResourceRoute(
        string $resource,
        string $filePath,
        array $options = []
    ): void {
        $dirName = Str::studly($resource);

        $routeFileName = Str::snake($resource).'-routes.php';

        self::invokeRoute("$filePath/$dirName/$routeFileName", $options);
    }

    public static function invokeWebRoute(string $resource, array $options = []): void
    {
        self::invokeResourceRoute($resource, 'Http', $options);
    }

    public static function invokeRoute(string $filePath, array $options = []): void
    {
        Route::group($options, app_path($filePath));
    }
}
