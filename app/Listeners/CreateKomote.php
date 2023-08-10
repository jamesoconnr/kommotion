<?php

namespace App\Listeners;

use App\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Komote;

class CreateKomote
{
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
    public function handle(Registered $event): void
    {
        $komote = new Komote();
        $komote->user_id = $event->user->id;
        $komote->content = 'Initial komote content';
        $komote->save();
    }
}
