<?php

use Livewire\Component;
use App\Models\user\User;
use App\Models\posts\Post;

use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component
{
    use WithPagination;

    public $profile;

    public $filterType;
    public $filterStatus;
    public $filterCategory;
    public $search;

    public function mount(User $user)
    {
        // This is the profile page, so we will just pass the authenticated user to the details component.
        if($user->id == auth()->user()->id) {
            $this->profile = auth()->user() ;
        } else {
            $this->profile = $user;
        }
    }

    #[Computed]
    public function posts(){

        return Post::where('user_id', $this->profile->id)
            ->orderBy('approved_at', 'desc')
            ->when($this->filterType, function ($query) {
                return $query->where('listing_type', $this->filterType);
            })
            ->when($this->filterCategory, function ($query) {
                return $query->whereIn('category', [$this->filterCategory]);
            })
            ->when($this->filterStatus, function ($query) {
                return $query->where('status', [$this->filterStatus]);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(5);
    }

    public function renewPost($postId)
    {
        $post = Post::findOrFail($postId);

        // Check if the authenticated user is the owner of the post
        if ($post->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the user has enough credits to renew the post
        if (auth()->user()->post_credits < 1) {
            Flux::toast('You do not have enough credits to renew this post.', 'Renewal Failed');
            return;
        }

        // Deduct 1 credit from the user's account
        auth()->user()->decrement('post_credits', 1);

        // Update the post's status to pending and reset the approved_at date
        $post->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        Flux::toast('Post renewed successfully.', 'Renewal Successful');
    }
};
