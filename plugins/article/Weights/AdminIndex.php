<?php

namespace Plugins\article\Weights;

use App\Services\Weight;

class AdminIndex extends Weight
{

    public function render()
    {
        return $this->fetch('admin_index');
    }
}
