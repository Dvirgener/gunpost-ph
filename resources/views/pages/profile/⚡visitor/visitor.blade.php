<div class="p-4 h-full flex flex-col">

    <div class="flex-1 min-h-0 overflow-y-scroll">
        <div>
            <livewire:pages::profile.details :user="$profile" />
        </div>

        <div class="px-2">
            <livewire:pages::profile.posts :user="$profile" />
        </div>
    </div>


</div>
