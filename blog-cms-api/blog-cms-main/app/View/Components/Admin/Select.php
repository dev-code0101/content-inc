<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Select extends Component
{
    public $label;
    public $name;
    public $options;
    public $value;
    public $required;
    public $placeholder;
    public $icon;

    public function __construct(
        $label,
        $name,
        $options = [],
        $value = '',
        $required = false,
        $placeholder = 'Select an option',
        $icon = null
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->options = $options;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('components.admin.select');
    }
}
