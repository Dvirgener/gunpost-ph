<?php

use App\Models\posts\Post;
use Livewire\Component;

new class extends Component {
    public Post $post;
    public string $category;

    public function mount(Post $post, $category)
    {
        $this->post = $post;
        $this->category = $category;

        // ensure the URL matches the stored category for safety
        abort_if($post->category !== $category, 404);

        // only owner can edit
        if (auth()->id() !== $post->user_id) {
            abort(403);
        }
    }
};
?>

<div>
    <livewire:pages::posts.edit.category.gun :post="$post" />
</div>
