<?php

namespace App\Contracts\Middleware;

interface Handler
{

    /**
     * Process the payload.
     *
     * @param mixed $payload
     * @param callable $next
     *
     * @return mixed
     */
    public function __invoke($payload, $next);
}