<?php

use App\Events\sendMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Flux\Flux;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\user\User;
use Jenssegers\Agent\Agent;

new class extends Component
{
    use WithPagination;


    #[Url(as: 'search')]
    public $search = ''; // This is used for searching conversations by message content.

    #[Url(as: 'filterPost')]
    public $filterPost = ''; // This is used for filtering conversations by post. It will be set to the post id when filtering by post.

    public $filter = 'all'; // This is used for filtering conversations by read/unread status. It can be set to 'unread' to show only conversations with unread messages, 'archive' to show only archived conversations, or 'all' to show all conversations.

    public $filterCategory = 'all'; // This is used for filtering conversations by category. It can be set to 'admin' to show only admin conversations, 'post' to show only post-related conversations, or 'all' to show all conversations.

    public $selectedConversation; // This is used for storing the currently selected conversation. It will be set to the conversation object when a conversation is selected.

    // Initiation Logic
    public $id;

    public $isMobile;

    public function mount(){

        $this->id = auth()->user()->id;
        $this->admins = User::where('account_type','=', 'TFT_admin')->get();
        $this->isMobile = $this->getIsMobileProperty();
    }


    public function getListeners()
    {
        return [
            "echo-private:ConversationUser." . $this->id . ",.new-message-event" => 'refreshConvoBox',
        ];
    }
    // Initiation Logic


    // ====================================================================================================================================================================>


    // PRIVATE METHODS =========================================================================================================================================================>

        private function markIncomingAsRead(): void
    {
        $unreadMessages = Message::where('conversation_id', '=', $this->selectedConversation->id)->whereJsonDoesntContain('read_by', auth()->user()->id)->get();

        foreach($unreadMessages as $msg){
            $msg->markAsRead(auth()->user()->id);
        }
    }

    private function getIsMobileProperty()
    {
        return (new Agent())->isMobile();
    }

    // PRIVATE METHODS =========================================================================================================================================================>

    #[On('seeConversation')]
    public function seeConversation(Conversation $conversation)
    {
        if($conversation == $this->selectedConversation){
            $this->selectedConversation = null;
            return;
        }
        $this->selectedConversation = $conversation;
        $this->markIncomingAsRead();
        // $this->refresh();
    }

    #[On('refreshConversations')]
    public function refresh()
    {
        $this->selectedConversation = null;
        unset($this->conversations);
        $this->resetPage();
    }

    // This method is used to refresh the conversation box when a new message is received. It checks if the new message belongs to the currently selected conversation, and if so, it marks the incoming messages as read and refreshes the conversation list.

    public function refreshConvoBox($event = null)
    {

        if($this->selectedConversation){
            if($event['conversation_id'] == $this->selectedConversation->id){
                $this->markIncomingAsRead();
            }
        }

        unset($this->conversations);

        $this->resetPage();
    }


    // SENDING MESSAGE TO ADMINS ================================================================>
    public $admins;
    public $chosenAdmin;
    public $adminMessage;
    public function sendAdminMessage()
    {
        $this->validate((['adminMessage' => 'required']));

        $existingCon = Conversation::query()
                        ->where('type', '=', 'admin')
                        ->where('initiator_id', '=', auth()->user()->id)
                        ->first();

        if($existingCon){
            $con = $existingCon;
        }else{
            $con = Conversation::query()
                ->where('type', '=', 'admin')
                ->where('initiator_id', '=', auth()->user()->id)
                ->firstOrCreate(['type' => 'admin', 'initiator_id' => auth()->user()->id]);

            $con->participants()->sync([$this->chosenAdmin, auth()->user()->id]);
        }


        $message = Message::create([
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

        sendMessage::dispatch($message);
    }

    #[Computed]
    public function conversations()
    {

        return Conversation::Query()
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
