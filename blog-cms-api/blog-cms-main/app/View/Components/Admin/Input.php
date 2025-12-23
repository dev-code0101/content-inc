<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Input extends Component
{
    public $label;
    public $name;
    public $value;
    public $type;
    public $icon;
    public $placeholder;
    public $required;

    public function __construct(
        $label,
        $name,
        $value = '',
        $type = 'text',
        $icon = null,
        $placeholder = '',
        $required = false
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->value = old($name, $value); // ✅ يفضل الـ old()
        $this->type = $type;
        $this->icon = $icon;
        $this->placeholder = $placeholder;
        $this->required = $required;
    }

    public function render()
    {
        return view('components.admin.input');
    }
}
