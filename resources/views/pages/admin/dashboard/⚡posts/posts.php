<?php

use Livewire\Component;
use Flux\Flux;

use App\Models\posts\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;

new class extends Component
{
        use WithPagination;

    public $allPostCount, $publishedPostCount, $draftPostCount, $closedPostCount, $archivedPostCount, $flaggedPostCount;

    public $postFilter = 'pending';
    public $search;
    public $postTypeFilter = 'all'; // all, sell, buy, trade
    public array $categoryFilters = ['gun','ammunition','airsoft','accessory','others'];

    #[On('refreshList')]
    public function mount()
    {
        $this->allPostCount = Post::count();
        $this->publishedPostCount = Post::where('status', 'approved')->count();
        $this->draftPostCount = Post::where('status', 'pending')->count();
        $this->closedPostCount = Post::where('status', 'expired')->count();
        $this->archivedPostCount = Post::where('status', 'archived')->count();
        $this->flaggedPostCount = Post::where('status', 'flagged')->count();


    }

    public function approvePost($postUuid)
    {
        $post = Post::where('id', $postUuid)->first();
        $post->status = 'approved';
        $post->approved_at = now();
        $post->approved_by = auth()->user()->id;
        $post->save();

        $post->user->post_credits -= 1;
        $post->user->save();

        Flux::toast(
            text: 'Post Approved.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
        $this->posts();


    }

    public function denyPost($postUuid)
    {
        $post = Post::where('id', $postUuid)->first();

        $post->status = "denied";
        $post->save();

        Flux::toast(
            text: 'Post Denied.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
        $this->posts();
    }

    public function updatePostFilter($filter)
    {
        $this->postFilter = $filter;
    }

    public function updatedCategoryFilters()
    {
        if (empty($this->categoryFilters)) {
            $this->categoryFilters = ['gun','ammunition','airsoft','accessory','others'];

        }
    }

    public function flagPost(Post $post)
    {
        $post->status = 'flagged'; // Change the post's status to flagged
        $post->save(); // Save the changes to the database
        Flux::toast(
            heading: 'Post Flagged!',
            text: 'You have successfully flagged the Post.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
    }

    public function unflagPost(Post $post)
    {
        $post->status = 'verified'; // Change the post's status to verified
        $post->save(); // Save the changes to the database
        Flux::toast(
            heading: 'Post Unflagged!',
            text: 'You have successfully unflagged the Post.',
            variant: 'success',
        );
        $this->dispatch('refreshList');
    }


    #[Computed()]
    public function posts()
    {
        return Post::orderBy('created_at', 'desc')
            ->when($this->postFilter != 'all', function ($query) {
                return $query->where('status', $this->postFilter);
            })
            ->when($this->postTypeFilter != 'all', function ($query) {
                return $query->where('listing_type', $this->postTypeFilter);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(5);
    }
};
