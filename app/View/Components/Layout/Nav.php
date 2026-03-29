<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class Nav extends Component
{
    public Collection $unreadNotifications;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->unreadNotifications = auth()->check()
            ? auth()->user()->unreadNotifications
            : new Collection;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout.nav');
    }
}
