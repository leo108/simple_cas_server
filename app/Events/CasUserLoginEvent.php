<?php

namespace App\Events;

use App\Events\Event;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CasUserLoginEvent extends Event {
    use SerializesModels;

    protected $request;
    protected $user;

    public function __construct(Request $request, User $user) {
        $this->request = $request;
        $this->user    = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn() {
        return [];
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }
}
