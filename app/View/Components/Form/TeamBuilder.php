<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class TeamBuilder extends Component
{
    public Collection $pokemon;

    /**
     * Create a new component instance.
     */
    public function __construct(?Collection $pokemon = null)
    {
        $this->pokemon = $pokemon ?? new Collection;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.team-builder');
    }
}
