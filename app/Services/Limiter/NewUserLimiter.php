<?php

namespace App\Services\Limiter;

use App\Models\UserParticipation;

class NewUserLimiter extends AbstractLimiter
{
    /**
     * @inheritDoc
     */
    protected function check($data)
    {
        if ($this->exists($data['type'], $data['user_id'])) {
            throw new LimitException();
        }
    }

    /**
     * @param int $type
     * @param int $userId
     * @return bool
     */
    protected function exists($type, $userId)
    {
        return UserParticipation::query()->where([
            'type' => $type,
            'user_id' => $userId,
        ])->exists();
    }
}
