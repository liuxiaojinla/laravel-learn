<?php

namespace App\Services\Limiter;

use App\Services\WithConfig;
use League\Pipeline\Pipeline;
use League\Pipeline\ProcessorInterface;
use League\Pipeline\StageInterface;

class LimitManager
{
    use WithConfig;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var callable[]
     */
    protected $limiters = [];

    /**
     * @var array
     */
    protected $namedStages = [
        'region' => RegionLimiter::class,
        'follow_official' => FollowOfficialLimiter::class,
        'location' => LocationLimiter::class,
        'new_user' => NewUserLimiter::class,
        'qy_friend' => QyFriendLimiter::class,
        'user_level' => UserLevelLimiter::class,
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 定义命名限制器
     * @param string $name
     * @param string|callable $stageProvider
     */
    public function named($name, $stageProvider)
    {
        $this->namedStages[$name] = $stageProvider;
    }

    /**
     * 添加一组限制器
     * @param callable[] $limiters
     * @return $this
     */
    public function appends(array $limiters)
    {
        foreach ($limiters as $limiter) {
            $this->append($limiter);
        }

        return $this;
    }

    /**
     * 添加一个限制器
     * @param StageInterface $limiter
     * @return $this
     */
    public function append(StageInterface $limiter)
    {
        $this->limiters[] = $limiter;

        return $this;
    }

    /**
     * 处理
     * @param array $payload
     * @return mixed
     */
    public function process($payload)
    {
        return (new Pipeline(
            $this->createProcessor(),
            ...$this->limiters()
        ))->process($payload);
    }

    /**
     * @return array
     */
    protected function limiters()
    {
        return array_merge(
            $this->prepareNamedLimiters(),
            ...$this->limiters,
        );
    }

    /**
     * @return array
     */
    protected function prepareNamedLimiters()
    {
        $stages = [];
        foreach ($this->config as $key => $config) {
            if (!isset($this->namedStages[$key])) {
                continue;
            }

            if (is_callable($this->namedStages[$key])) {
                $stage = call_user_func($this->namedStages[$key], $config);
            } else {
                $class = $this->namedStages[$key];
                $stage = new $class($config);
            }

            $stages[] = $stage;
        }

        return $stages;
    }

    /**
     * @return ProcessorInterface|null
     */
    protected function createProcessor()
    {
        return null;
    }
}
