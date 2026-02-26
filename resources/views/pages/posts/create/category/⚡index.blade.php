<?php

use Livewire\Component;

new class extends Component
{
    public $category;

    public function mount($category){
        $this->category = $category;
    }
};
?>

<div>

    <livewire:pages::posts.create.category.gun/>

</div>
