<?php

namespace App\Events;

use App\Concerns\ConstProperty;
use App\Models\User as UserModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class Login
 *
 * @property-read UserModel $user
 */
class Login{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    use ConstProperty;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct(UserModel $user){
        $this->initializeConstProperty([
            'user' => $user,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(){
        return new PrivateChannel('channel-name');
    }
}
