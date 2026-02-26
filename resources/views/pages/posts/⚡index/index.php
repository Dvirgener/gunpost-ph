<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\posts\Post;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function posts(){
        return Post::all();
    }


};
