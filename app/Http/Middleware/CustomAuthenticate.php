<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomAuthenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // Don't redirect, just return null so Laravel returns 401 JSON response
        return null;
    }
}
