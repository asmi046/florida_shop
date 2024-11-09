<?php

namespace App\View\Components;

use App\Models\Celebration;
use Illuminate\View\Component;

class TegsMain extends Component
{
    public $all_tag;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->all_tag = Celebration::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tegs-main');
    }
}
