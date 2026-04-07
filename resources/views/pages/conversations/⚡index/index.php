<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\user\User;

new class extends Component
{
    use WithPagination;

    public $conversation;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'filterPost')]
    public $filterPost = '';

    public $filter = 'all';

    public $filterCategory = 'all';

    public function seeConversation($conversation)
    {
        $this->dispatch('open-convo', ['conversation' => $conversation]);
        $this->dispatch('refreshThis');
    }

    public function deleteConvo(Conversation $conversation)
    {
        $conversation->participants()->detach();
        $conversation->messages()->delete();
        $conversation->delete();
        $this->dispatch('refreshThis');
    }

    public function refreshConvoBox(Conversation $conversation)
    {
        $ids = $conversation->participants()->pluck('user_id')->toArray();
        if (in_array(auth()->user()->id, $ids)) {
            $this->conversations();
        }
    }

    public $adminMessage;
    public function sendAdminMessage()
    {
        $adminIds = User::query()
            ->where('classification', '=', 'TFT_admin')
            ->pluck('id')
            ->toArray();

        $this->validate((['adminMessage' => 'required']));

        $con = Conversation::query()
            ->where('type', '=', 'admin')
            ->where('initiator_id', '=', auth()->user()->id)
            ->firstOrCreate(['type' => 'admin', 'initiator_id' => auth()->user()->id]);

        $con->participants()->sync([...$adminIds, auth()->user()->id]);

        Message::create([
            'conversation_id' => $con->id,
            'sender_id' => auth()->user()->id,
            'body' => $this->adminMessage,
            'read_by' => [auth()->user()->id]
        ]);

        Flux::toast(
            heading: 'Message Sent to Admin!',
            text: 'Thank you for sending us a Message. an admin will get back to you later.',
            variant: 'success',
        );
        $this->modal('sendAdminMessage')->close();
        $this->adminMessage = '';

        // NewConversation::dispatch($con);
    }

    #[Computed]
    public function conversations()
    {

        return Conversation::Query()
            ->withCount([
                'messages as unread_count' => fn($q) =>
                    $q->whereJsonDoesntContain('read_by', auth()->user()->id)
            ])
            ->whereHas('participants', function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->when($this->filterPost, function ($query) {
                return $query->whereHas('post', function ($q) {
                    $q->where('id', $this->filterPost);
                });
            })
            ->when($this->filter == 'unread', function ($query) {
                return $query->whereHas('messages', function ($q) {
                    $q->whereJsonDoesntContain('read_by', auth()->user()->id);
                });
            })->when($this->filterCategory != 'all', function ($query) {
                return $query->where('type', $this->filterCategory);
            })->when($this->filter == 'archive', function ($query) {
                return $query->whereHas('post', function ($q) {
                    $q->where('status', '=', $this->filter);
                });
            })->when($this->search, function ($query) {
                $query->whereHas('messages', function ($q) {
                    $q->where('body', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }
};
