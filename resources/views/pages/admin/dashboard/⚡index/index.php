<?php

use Livewire\Component;

new class extends Component
{
    public $test = 'Hello World';

    public function mount(){
        $this->test = 'wew';
    }
};
