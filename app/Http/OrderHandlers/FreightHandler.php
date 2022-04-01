<?php

namespace App\Http\OrderHandlers;

class FreightHandler extends AbstractHandler
{
    protected function handle()
    {
        $this->payload['freight_price'] = 0;
    }
}
