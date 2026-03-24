<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex flex-col justify-center pt-20">

    <div class="flex justify-end w-full pe-10 pb-20">
        <flux:button href="{{ route('posts') }}" variant="primary" color="red">Back</flux:button>
    </div>

    <div class="text-center">
        <h1 class="text-3xl font-bold mb-5">Create a Post</h1>
        <p class="text-gray-500 mb-10">Select a category to create a post in.</p>
    </div>

    <div class="flex items-center gap-10 px-10 rounded-md py-10 h-100">
        <x-virg.create-post-button name="Gun" link="{{ route('posts.create.category.gun') }}" />
        <x-virg.create-post-button name="Ammo"
            link="{{ route('posts.create.category.ammunition', ['category' => 'ammunition']) }}" />


        {{-- <x-virg.create-post-button name="Airsoft"
            link="{{ route('posts.create.category.index', ['category' => 'airsoft']) }}" />
        <x-virg.create-post-button name="Accessory"
            link="{{ route('posts.create.category.index', ['category' => 'accessory']) }}" />
        <x-virg.create-post-button name="Other"
            link="{{ route('posts.create.category.index', ['category' => 'other']) }}" /> --}}
    </div>

</div>
