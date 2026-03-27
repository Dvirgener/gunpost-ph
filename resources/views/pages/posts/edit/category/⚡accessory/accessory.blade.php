<div class="max-w-5xl mx-auto space-y-6">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Edit Accessory Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>
            <flux:badge color="blue">Category: Accessory</flux:badge>
        </div>
    </flux:card>

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
                                <img src="{{ asset('storage/' . $this->uploaded_primary_photo) }}" alt="Photo 1 preview"
                                    class="object-cover w-15 h-15" />

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
                                            <flux:file-item.remove wire:click="removeOtherPhoto({{ $index }})" />
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

            <!-- Listing -->
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
                            <flux:input wire:model.live="title" placeholder="e.g., Aimpoint PRO Red Dot Sight" />
                            <flux:error name="title" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Description <span class="text-red-500">*</span></flux:label>
                            <flux:textarea wire:model.live="description" rows="5"
                                placeholder="Condition, features, compatibility, shipping notes" />
                            <flux:error name="description" />
                        </flux:field>

                        <flux:field>
                            <flux:label>General condition <span class="text-red-500">*</span></flux:label>
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
                                <flux:label>Price <span class="text-red-500">*</span></flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="price" placeholder="0.00" />
                                <flux:error name="price" />
                            </flux:field>
                        @elseif($this->listing_type === 'buy')
                            <flux:field>
                                <flux:label>Budget min <span class="text-red-500">*</span></flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="buy_min_price"
                                    placeholder="0.00" />
                                <flux:error name="buy_min_price" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Budget max <span class="text-red-500">*</span></flux:label>
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

            <!-- Accessory identification -->
            <flux:accordion.item expanded>
                <flux:accordion.heading class="text-blue-500! mb-3">Accessory identification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Accessory category</flux:label>
                            <flux:select wire:model.live="accessory_category">
                                <option value="">— Optional —</option>
                                @foreach ($this->accessoryCategoryOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="accessory_category" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Brand</flux:label>
                            <flux:input wire:model.live="brand" placeholder="e.g., Aimpoint" />
                            <flux:error name="brand" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Model</flux:label>
                            <flux:input wire:model.live="model" placeholder="e.g., PRO" />
                            <flux:error name="model" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>

            <!-- Compatibility & Fitment -->
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Compatibility & Fitment</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Compatible with</flux:label>
                            <flux:input wire:model.live="compatible_with" placeholder="e.g., Glock 19, Picatinny" />
                            <flux:error name="compatible_with" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Mount type</flux:label>
                            <flux:select wire:model.live="mount_type">
                                <option value="">— Optional —</option>
                                @foreach ($this->mountTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="mount_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Size</flux:label>
                            <flux:input wire:model.live="size" placeholder="e.g., S/M/L or dimensions" />
                            <flux:error name="size" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>

            <!-- Materials & Finish -->
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Materials & Finish</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Color</flux:label>
                            <flux:input wire:model.live="color" placeholder="e.g., Black" />
                            <flux:error name="color" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Material</flux:label>
                            <flux:input wire:model.live="material" placeholder="e.g., Polymer, Aluminum" />
                            <flux:error name="material" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>

            <!-- Commercial Identifiers -->
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Commercial Identifiers</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>SKU</flux:label>
                            <flux:input wire:model.live="sku" placeholder="Manufacturer SKU" />
                            <flux:error name="sku" />
                        </flux:field>
                        <flux:field>
                            <flux:label>UPC</flux:label>
                            <flux:input wire:model.live="upc" placeholder="UPC code" />
                            <flux:error name="upc" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>

            <!-- Package -->
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Package includes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:field class="md:col-span-3">
                        <flux:label>Package includes</flux:label>
                        <flux:textarea wire:model.live="package_includes" rows="3"
                            placeholder="What comes with this accessory?" />
                        <flux:error name="package_includes" />
                    </flux:field>
                </flux:accordion.content>
            </flux:accordion.item>

            <!-- Notes -->
            <flux:accordion.item>
                <flux:accordion.heading class="text-blue-500! mb-3">Notes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:field class="md:col-span-3">
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model.live="notes" rows="4"
                            placeholder="Additional notes or specifications" />
                        <flux:error name="notes" />
                    </flux:field>
                </flux:accordion.content>
            </flux:accordion.item>

        </flux:accordion>

        <div class="flex gap-3 justify-end">
            <flux:button type="submit" variant="primary">Update Post</flux:button>
            <flux:button
                href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
                variant="ghost">← Back </flux:button>
        </div>
    </form>
</div>
