<div class="max-w-5xl mx-auto space-y-6">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Create Ammunition Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Ammunition</flux:badge>
        </div>
    </flux:card>

    <form action="" wire:submit="save" class="space-y-4">
        <flux:accordion>


            {{-- Photos (p_1..p_10) --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Photos</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Repeat for each slot --}}
                        <div class="space-y-2">
                            <flux:text class="text-gray-600 dark:text-white/60">Upload a Primary Picture for your post.
                            </flux:text>
                            <flux:file-upload wire:model="primary_photo" label="Primary Photo">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            @if ($primary_photo)
                                <flux:file-item :heading="$primary_photo->getClientOriginalName()"
                                    :image="$primary_photo->temporaryUrl()" :size="$primary_photo->getSize()"
                                    class="flex items-center">
                                    <x-slot name="actions">
                                        <flux:file-item.remove wire:click="removePrimaryPhoto" />
                                    </x-slot>
                                </flux:file-item>
                            @endif

                            {{-- <flux:error name="primary_photo" /> --}}
                        </div>

                        <div class="space-y-2">
                            <flux:text class="text-gray-600 dark:text-white/60">You can also upload additional photos
                                (up to 9).
                            </flux:text>
                            <flux:file-upload wire:model="other_photos" multiple label="Other Photos">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            @if ($other_photos)
                                @foreach ($other_photos as $index => $photo)
                                    <flux:file-item :heading="$photo->getClientOriginalName()"
                                        :image="$photo->temporaryUrl()" :size="$photo->getSize()"
                                        class="flex items-center">
                                        <x-slot name="actions">
                                            <flux:file-item.remove wire:click="removeOtherPhoto({{ $index }})" />
                                        </x-slot>
                                    </flux:file-item>
                                @endforeach

                            @endif
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Photos (p_1..p_10) --}}


            {{-- Listing --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Listing details</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Listing type <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model.live="listing_type">
                                @foreach ($this->listingTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="listing_type" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Title <span class="text-red-500"> *</span></flux:label>
                            <flux:input wire:model.live="title" placeholder="e.g., Glock 19 Gen 5 w/ extras" />
                            <flux:error name="title" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Description <span class="text-red-500"> *</span></flux:label>
                            <flux:textarea wire:model.live="description" rows="6"
                                placeholder="Condition, inclusions, history, meetup/shipping notes..." />
                            <flux:error name="description" />
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                General condition
                                <span class="text-red-500"> *</span>
                            </flux:label>

                            <flux:select wire:model.live="post_condition">
                                <option value="">— Optional —</option>
                                @foreach ($this->postConditionOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="post_condition" />
                        </flux:field>

                        @if ($this->listing_type === 'sell')
                            <flux:field>
                                <flux:label>
                                    Price
                                    <span class="text-red-500"> *</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="price" placeholder="0.00" />
                                <flux:error name="price" />
                            </flux:field>
                        @elseif($this->listing_type === 'buy')
                            <flux:field>
                                <flux:label>
                                    Budget range (min)
                                    <span class="text-red-500"> *</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="buy_min_price"
                                    placeholder="0.00" />
                                <flux:error name="buy_min_price" />
                            </flux:field>

                            <flux:field>
                                <flux:label>
                                    Budget range (max)
                                    <span class="text-red-500"> *</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="buy_max_price"
                                    placeholder="0.00" />
                                <flux:error name="buy_max_price" />
                            </flux:field>
                        @endif





                        <div class="flex items-center gap-3 mt-7">
                            <flux:switch wire:model.live="is_negotiable" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Negotiable</div>
                                <div class="text-xs text-zinc-500">Allow offers on this listing.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Listing --}}


            {{-- Ammunition identification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Ammunition identification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Brand <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="brand" placeholder="e.g., Federal, Winchester" />
                            <flux:error name="brand" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Product line <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="product_line" placeholder="e.g., American Eagle" />
                            <flux:error name="product_line" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Caliber <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="caliber" placeholder="e.g., 9mm, .45 ACP" />
                            <flux:error name="caliber" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Ammunition identification --}}

            {{-- Ammunition specs --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Ammunition specifications</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Bullet type <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="bullet_type" placeholder="e.g., FMJ, JHP, FMJBT" />
                            <flux:error name="bullet_type" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Grain (weight)</flux:label>
                            <flux:input wire:model.live="grain" placeholder="e.g., 115gr, 147gr" />
                            <flux:error name="grain" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Case material</flux:label>
                            <flux:input wire:model.live="case_material" placeholder="e.g., Brass, Steel" />
                            <flux:error name="case_material" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Primer type</flux:label>
                            <flux:input wire:model.live="primer_type" placeholder="e.g., Boxer, Berdan" />
                            <flux:error name="primer_type" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Ammunition specs --}}

            {{-- Quantity & packaging --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Quantity & packaging</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Total rounds</flux:label>
                            <flux:input type="number" wire:model.live="total_rounds" placeholder="e.g., 500" />
                            <flux:error name="total_rounds" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Number of boxes</flux:label>
                            <flux:input type="number" wire:model.live="boxes" placeholder="e.g., 5" />
                            <flux:error name="boxes" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Rounds per box</flux:label>
                            <flux:input type="number" wire:model.live="rounds_per_box" placeholder="e.g., 100" />
                            <flux:error name="rounds_per_box" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Quantity & packaging --}}

            {{-- Lot & metadata --}}
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Lot & SKU information</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Lot number</flux:label>
                            <flux:input wire:model.live="lot_number" placeholder="e.g., LOT123456" />
                            <flux:error name="lot_number" />
                        </flux:field>
                        <flux:field>
                            <flux:label>SKU</flux:label>
                            <flux:input wire:model.live="sku" />
                            <flux:error name="sku" />
                        </flux:field>
                        <flux:field>
                            <flux:label>UPC</flux:label>
                            <flux:input wire:model.live="upc" />
                            <flux:error name="upc" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Lot & metadata --}}

            {{-- Condition & special --}}
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Condition & special flags</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Condition</flux:label>
                            <flux:select wire:model.live="condition">
                                <option value="">— Optional —</option>
                                <option value="factory_new">Factory New</option>
                                <option value="sealed">Sealed</option>
                                <option value="opened">Opened</option>
                                <option value="mixed">Mixed</option>
                                <option value="other">Other</option>
                            </flux:select>
                            <flux:error name="condition" />
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="corrosive" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Corrosive</div>
                                <div class="text-xs text-zinc-500">Contains corrosive primers.</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="reloads" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Reloads</div>
                                <div class="text-xs text-zinc-500">Reloaded ammunition.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Condition & special --}}

            {{-- Notes --}}
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Additional notes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:field class="md:col-span-3">
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model.live="notes" rows="4"
                            placeholder="Any additional information..." />
                        <flux:error name="notes" />
                    </flux:field>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Notes --}}

        </flux:accordion>

        <div class="flex items-center justify-end gap-3">

            <flux:button type="submit" variant="primary" class="hover:cursor-pointer">
                Submit for review
            </flux:button>

            <flux:button href="{{ route('posts') }}" type="button" variant="ghost">← Back </flux:button>

        </div>
    </form>
</div>
