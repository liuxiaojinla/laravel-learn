<?php

namespace Plugins\hello;

use App\Services\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    /**
     * @var array
     */
    protected $info = [
        'name' => 'hello',
        'description' => '',
        'tasks' => [],
        'events' => [],
    ];

    /**
     * @inheritDoc
     */
    public function install()
    {
        // TODO: Implement install() method.
    }

    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }
}
