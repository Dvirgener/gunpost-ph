<div class="p-4 flex flex-col h-full">
    <div class="flex justify-between mb-5">
        <h1 class="font-bold text-2xl">ADMIN DASHBOARD</h1>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto px-2">
        <flux:tab.group>
            <flux:tabs>
                <flux:tab name="overview" icon="view-columns">Overview</flux:tab>
                <flux:tab name="account" icon="bell">Site Activities</flux:tab>
                <flux:tab name="billing" icon="banknotes">Revenue</flux:tab>
            </flux:tabs>


            <flux:tab.panel name="overview">
                <livewire:pages::admin.partitions.overview />
            </flux:tab.panel>
            <flux:tab.panel name="account">(Coding ....)</flux:tab.panel>
            <flux:tab.panel name="billing">(Coding ....)</flux:tab.panel>
        </flux:tab.group>
    </div>


</div>
