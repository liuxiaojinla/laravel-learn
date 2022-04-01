<?php

namespace App\Http\RepositoryHandlers;

use function dump;

class GlobalMiddlewareHandler
{
    public function filterable($input, $next)
    {
        dump("global filterable dynamic.");

        return $next($input);
    }

    public function detailable($input, $next)
    {
        dump('global detailable dynamic.');

        return $next($input);
    }

    public function showable($input, $next)
    {
        dump('global showable dynamic.');

        return $next($input);
    }


    public function storeable($input, $next)
    {
        dump('global storeable dynamic.');

        return $next($input);
    }


    public function updateable($input, $next)
    {
        dump('global updateable dynamic.');

        return $next($input);
    }


    public function deleteable($input, $next)
    {
        dump('global deleteable dynamic.');

        return $next($input);
    }


    public function recoveryable($input, $next)
    {
        dump('global recoveryable dynamic.');

        return $next($input);
    }


    public function restoreable($input, $next)
    {
        dump('global restoreable dynamic.');

        return $next($input);
    }


    public function validateable($input, $next)
    {
        dump('global validateable dynamic.');

        return $next($input);
    }
}