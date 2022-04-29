<?php

namespace PHPSTORM_META {
    override(new \Illuminate\Contracts\Container\Container, map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\Illuminate\Container\Container::makeWith(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\Illuminate\Contracts\Container\Container::get(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\Illuminate\Contracts\Container\Container::make(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\Illuminate\Contracts\Container\Container::makeWith(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\App::get(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\App::make(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\App::makeWith(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\app(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\resolve(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\resolve(0), map([
        'App\Http\Request' => \App\Http\Request::class,
        'Illuminate\Http\Request' => \App\Http\Request::class,
    ]));

    override(\App\Http\Request::module(0), string);
}