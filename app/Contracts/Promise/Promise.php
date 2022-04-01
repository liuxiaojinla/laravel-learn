<?php

namespace App\Contracts\Promise;

interface Promise
{
    /**
     * 当前任务是否等待中（前端展示应统一展示为进行中）
     * @return bool
     */
    public function isWaiting();

    /**
     * 当前任务是否为进行中（前端展示应统一展示为进行中）
     * @return bool
     */
    public function isPending();

    /**
     * 当前任务是否已结束（包含已失败的）
     * @return bool
     */
    public function isCompleted();

    /**
     * 是否已成功
     * @return bool
     */
    public function isSucceeded();

    /**
     * 是否已失败
     * @return bool
     */
    public function isFailed();

    /**
     * 当前任务是否已手动停止（用户是否已手动停止）
     * @return bool
     */
    public function isStopped();

    /**
     * 当前任务是否已过期
     * @return bool
     */
    public function isExpired();

    /**
     * 当前任务状态
     * @param int $status
     * @return bool
     */
    public function isStatus($status);

    /**
     * 设置为等待状态
     * @param array $attributes
     * @return bool
     * @deprecated 不建议使用，如果任务需要重试，建议新创建一条记录
     */
    public function setWaitingStatus($attributes = []);

    /**
     * 设置任务为进行中的状态
     * @param array $attributes
     * @return bool
     */
    public function setPendingStatus($attributes = []);

    /**
     * 设置失败的状态
     * @param string $error
     * @param array $attributes
     * @return bool
     */
    public function setFailedStatus($error, $attributes = []);

    /**
     * 设置成功的状态
     * @param array $attributes
     * @return bool
     */
    public function setSucceededStatus($attributes = []);

    /**
     * 设置任务为已结束状态
     * @param string $error
     * @param array $attributes
     * @return bool
     */
    public function setStoppedStatus($error = null, $attributes = []);

    /**
     * 实例化新任务（等待状态）
     * @param array $attributes
     * @return $this
     */
    public function withWaitingStatus($attributes = []);

    /**
     * 实例化新任务（进行中状态）
     * @param array $attributes
     * @return $this
     */
    public function withPendingStatus($attributes = []);

    /**
     * 获取进度条进度
     * @return float
     */
    public function getProgress();

    /**
     * 设置进度条
     * @param float $progress
     */
    public function setProgress($progress);

    /**
     * 获取进度条进度
     * @return float
     */
    public function getProgressCount();

    /**
     * 根据已执行的数量设置进度
     * @param int $count
     */
    public function setProgressByCount($count);

    /**
     * 根据已执行的数量设置进度（自增）
     * @param string $count
     * @return string
     */
    public function setProgressByIncCount($count);
}