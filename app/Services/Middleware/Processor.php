<?php

namespace App\Services\Middleware;

use Closure;
use League\Pipeline\ProcessorInterface;

class Processor implements ProcessorInterface
{
    /**
     * @var Closure
     */
    protected $destination;

    /**
     * @param Closure $destination
     */
    public function __construct(Closure $destination)
    {
        $this->destination = $destination;
    }

    /**
     * @inerhitDoc
     */
    public function process($payload, callable ...$stages)
    {
        $pipeline = array_reduce(
            array_reverse($stages),
            $this->carry(),
            $this->prepareDestination($this->destination)
        );

        return $pipeline($payload);
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                try {
                    return $pipe($passable, $stack);
                } catch (\Exception $e) {
                    return $this->handleException($passable, $e);
                }
            };
        };
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param \Closure $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            try {
                return $destination($passable);
            } catch (\Exception $e) {
                return $this->handleException($passable, $e);
            }
        };
    }

    /**
     * Handle the given exception.
     *
     * @param mixed $passable
     * @param \Exception $e
     * @return mixed
     */
    protected function handleException($passable, \Exception $e)
    {
        throw $e;
    }
}
