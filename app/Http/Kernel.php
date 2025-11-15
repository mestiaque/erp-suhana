<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\AuthorizationMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // ...existing middleware...
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ...existing middleware...
            \App\Http\Middleware\LocaleMiddleware::class,
        ],

        'api' => [
            // ...existing middleware...
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // ...existing aliases...
        'active.user' => CheckUserActive::class,
        'authorization' => AuthorizationMiddleware::class,
    ];

    // protected $routeMiddleware = [
    //     // ...existing middleware...
    //     'active.user' => CheckUserActive::class,
    //     'authorization' => AuthorizationMiddleware::class,
    // ];
}

