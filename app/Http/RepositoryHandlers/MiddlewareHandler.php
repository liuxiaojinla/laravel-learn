<?php

namespace App\Http\RepositoryHandlers;

use function dump;

class MiddlewareHandler
{
    public function filterable($input, $next)
    {
        dump('filterable dynamic.');

        return $next($input);
    }

    public function detailable($input, $next)
    {
        dump('detailable dynamic.');

        return $next($input);
    }

    public function showable($input, $next)
    {
        dump('showable dynamic.');

        return $next($input);
    }

    public function storeable($input, $next)
    {
        dump('storeable dynamic.');

        return $next($input);
    }

    public function updateable($input, $next)
    {
        dump('updateable dynamic.');

        return $next($input);
    }

    public function deleteable($input, $next)
    {
        dump('deleteable dynamic.');

        return $next($input);
    }

    public function recoveryable($input, $next)
    {
        dump('recoveryable dynamic.');

        return $next($input);
    }

    public function restoreable($input, $next)
    {
        dump('restoreable dynamic.');

        return $next($input);
    }

    public function validateable($input, $next)
    {
        dump('validateable dynamic.');

        return $next($input);
    }
}
