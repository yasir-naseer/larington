<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/cart/get/clubs',
        '/cart/apply/points',
        '/cart/apply/pin',
        '/merchants',
        '/products'
        
    ];
}
