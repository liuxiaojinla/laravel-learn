<?php

namespace App\Events\Plugin;

use Illuminate\Console\Scheduling\Schedule;

/**
 * @mixin Schedule
 */
class ScheduleDefined
{
    protected Schedule $schedule;

    /**
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return \Illuminate\Console\Scheduling\Schedule
     */
    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->schedule, $name], $arguments);
    }
}
