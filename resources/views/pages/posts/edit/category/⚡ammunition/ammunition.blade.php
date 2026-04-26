<div class="max-w-5xl mx-auto space-y-6 flex flex-col h-full">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Edit Ammunition Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Ammunition</flux:badge>
        </div>
    </flux:card>

    <div class="flex-1 h-min-0 overflow-y-scroll px-2">
        <form wire:submit="save" class="space-y-6">
            <flux:accordion>

                {{-- Photos (p_1..p_10) --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Photos</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Repeat for each slot --}}
                            <div class="space-y-2">
                                <div class="h-10">
                                    <flux:text class="text-xs text-red-500">Note: Replace the Primary
                                        Photo of
                                        your post
                                    </flux:text>
                                </div>

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

                                <flux:text>Current upload:</flux:text>
                                <div
                                    class="w-full py-1 rounded-md border shadow flex justify-between items-center px-3 dark:bg-stone-800 dark:border-stone-700/60 dark:shadow-stone-900/50">
                                    <img src="{{ asset('storage/' . $this->uploaded_primary_photo) }}"
                                        alt="Photo 1 preview" class="object-cover w-15 h-15" />

                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="h-10">
                                    <flux:text class="text-red-500 text-xs">Note: If you want to replace or add
                                        more
                                        photos, upload them here. You can upload up to 9 additional photos but you must
                                        delete
                                        the existing ones first.</flux:text>
                                </div>

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
                                                <flux:file-item.remove
                                                    wire:click="removeOtherPhoto({{ $index }})" />
                                            </x-slot>
                                        </flux:file-item>
                                    @endforeach
                                @endif

                                <flux:text>Current upload:</flux:text>
                                @foreach ($uploaded_other_photos as $index => $photo)
                                    @if ($photo)
                                        <div
                                            class="w-full py-1 rounded-md border shadow flex justify-between items-center px-3 dark:bg-stone-800 dark:border-stone-700/60 dark:shadow-stone-900/50">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Photo 1 preview"
                                                class="object-cover w-15 h-15" />

                                            <flux:button icon="x-mark" class="hover:cursor-pointer"
                                                wire:click="removePhoto({{ $index }})"></flux:button>

                                        </div>
                                    @endif
                                @endforeach


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
                                <flux:label>Title <span class="text-red-500">*</span></flux:label>
                                <flux:input wire:model.live="title" placeholder="e.g., 9mm FMJ 115gr - 1000 rounds" />
                                <flux:error name="title" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Description <span class="text-red-500">*</span></flux:label>
                                <flux:textarea wire:model.live="description" rows="6"
                                    placeholder="Condition, packaging, storage history, shipping notes..." />
                                <flux:error name="description" />
                            </flux:field>

                            <flux:field>
                                <flux:label>
                                    General condition
                                    <span class="text-red-500">*</span>
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
                                        <span class="text-red-500">*</span>
                                    </flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="price"
                                        placeholder="0.00" />
                                    <flux:error name="price" />
                                </flux:field>
                            @elseif($this->listing_type === 'buy')
                                <flux:field>
                                    <flux:label>
                                        Budget range (min)
                                        <span class="text-red-500">*</span>
                                    </flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="buy_min_price"
                                        placeholder="0.00" />
                                    <flux:error name="buy_min_price" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>
                                        Budget range (max)
                                        <span class="text-red-500">*</span>
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
                    <flux:accordion.heading class="text-blue-500! mb-3">Ammunition identification
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Brand</flux:label>
                                <flux:input wire:model.live="brand" placeholder="e.g., Federal, Winchester" />
                                <flux:error name="brand" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Product line</flux:label>
                                <flux:input wire:model.live="product_line"
                                    placeholder="e.g., Gold Medal, American Eagle" />
                                <flux:error name="product_line" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Caliber</flux:label>
                                <flux:input wire:model.live="caliber" placeholder="e.g., 9mm, .45 ACP, 5.56 NATO" />
                                <flux:error name="caliber" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Ammunition identification --}}

                {{-- Specifications --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Specifications</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Bullet type</flux:label>
                                <flux:select wire:model.live="bullet_type">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->bulletTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="bullet_type" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Grain</flux:label>
                                <flux:input wire:model.live="grain" placeholder="e.g., 115gr, 124gr" />
                                <flux:error name="grain" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Case material</flux:label>
                                <flux:select wire:model.live="case_material">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->caseMaterialOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="case_material" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Primer type</flux:label>
                                <flux:select wire:model.live="primer_type">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->primerTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="primer_type" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-7">
                                <flux:switch wire:model.live="corrosive" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Corrosive</div>
                                    <div class="text-xs text-zinc-500">Contains corrosive primers.</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Specifications --}}

                {{-- Quantity & Packaging --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Quantity & Packaging</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Total rounds</flux:label>
                                <flux:input type="number" wire:model.live="total_rounds" placeholder="e.g., 1000" />
                                <flux:error name="total_rounds" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Number of boxes</flux:label>
                                <flux:input type="number" wire:model.live="boxes" placeholder="e.g., 10" />
                                <flux:error name="boxes" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Rounds per box</flux:label>
                                <flux:input type="number" wire:model.live="rounds_per_box" placeholder="e.g., 50" />
                                <flux:error name="rounds_per_box" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Quantity & Packaging --}}

                {{-- Lot & SKU Information --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Lot & SKU Information</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Lot number</flux:label>
                                <flux:input wire:model.live="lot_number" placeholder="e.g., LOT12345" />
                                <flux:error name="lot_number" />
                            </flux:field>

                            <flux:field>
                                <flux:label>SKU</flux:label>
                                <flux:input wire:model.live="sku" placeholder="Stock keeping unit" />
                                <flux:error name="sku" />
                            </flux:field>

                            <flux:field>
                                <flux:label>UPC</flux:label>
                                <flux:input wire:model.live="upc" placeholder="Universal product code" />
                                <flux:error name="upc" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Condition</flux:label>
                                <flux:select wire:model.live="condition">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->conditionOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="condition" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-7">
                                <flux:switch wire:model.live="reloads" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Reloads</div>
                                    <div class="text-xs text-zinc-500">Contains reloaded ammunition.</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Lot & SKU Information --}}

                {{-- Additional Notes --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Additional Notes</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:field>
                            <flux:label>Notes</flux:label>
                            <flux:textarea wire:model.live="notes" rows="4"
                                placeholder="Any additional information about the ammunition..." />
                            <flux:error name="notes" />
                        </flux:field>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Additional Notes --}}

            </flux:accordion>

            <div class="flex justify-end gap-3">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Update Post</flux:button>
                <flux:button
                    href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
                    variant="ghost">← Back </flux:button>
            </div>
        </form>
    </div>


</div>
