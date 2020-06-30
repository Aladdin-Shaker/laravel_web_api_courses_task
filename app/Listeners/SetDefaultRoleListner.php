<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetDefaultRoleListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreatedEvent  $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        $role = Role::where('role', 'student')->firstOrFail();
        $event->user->roles()->attach($role->id);
    }
}
