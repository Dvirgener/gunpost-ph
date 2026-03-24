<?php

use Livewire\Component;

new class extends Component {
    public $category;

    public function mount($category)
    {
        $this->category = $category;
    }
};
?>

<div>

    @switch($this->category)
        @case('gun')
            <livewire:pages::posts.create.category.gun />
        @break

        @case('ammunition')
            <livewire:pages::posts.create.category.ammunition />
        @break

        @case('airsoft')
            <livewire:pages::posts.create.category.airsoft />
        @break

        @default
            <div class="text-center py-12">
                <p class="text-gray-600">Category not found</p>
            </div>
    @endswitch

</div>
