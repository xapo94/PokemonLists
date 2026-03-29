<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use App\Notifications\TeamCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTeamCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TeamCreated $event): void
    {
        $event->team->user->notify(new TeamCreatedNotification($event->team));
    }
}
