<?php

namespace App\Http\OrderHandlers;

use App\Jobs\Orders\AutoClose;

class AutoCloseHandler extends AbstractHandler
{
    protected function response()
    {
        if ($this->isPreview()) {
            return;
        }

        AutoClose::dispatch($this->response['id']);
    }
}
