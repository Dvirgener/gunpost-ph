<div class="max-w-5xl mx-auto space-y-6">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Edit Gun Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Gun</flux:badge>
        </div>
    </flux:card>

    <form wire:submit="save" class="space-y-6">
        <flux:accordion>

            {{-- Photos (p_1..p_10) --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Photos</flux:accordion.heading>
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


            {{-- Listing --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Listing details</flux:accordion.heading>
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

                        {{-- <flux:field>
                            <flux:label>Location</flux:label>
                            <flux:input wire:model.live="location" placeholder="City / Province (optional)"/>
                            <flux:error name="location"/>
                        </flux:field> --}}

                        <flux:field class="md:col-span-2">
                            <flux:label>Title <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="title" placeholder="e.g., Glock 19 Gen 5 w/ extras" />
                            <flux:error name="title" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Description <span class="text-red-500">*</span></flux:label>
                            <flux:textarea wire:model.live="description" rows="6"
                                placeholder="Condition, inclusions, history, meetup/shipping notes..." />
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
                                <flux:input type="number" step="0.01" wire:model.live="price" placeholder="0.00" />
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


            {{-- 2) Gun identification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Gun identification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Manufacturer</flux:label>
                            <flux:input wire:model.live="manufacturer" placeholder="e.g., Glock" />
                            <flux:error name="manufacturer" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Model</flux:label>
                            <flux:input wire:model.live="model" placeholder="e.g., 19" />
                            <flux:error name="model" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Variant</flux:label>
                            <flux:input wire:model.live="variant" placeholder="e.g., Gen 5 / MOS" />
                            <flux:error name="variant" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Series</flux:label>
                            <flux:input wire:model.live="series" placeholder="Optional" />
                            <flux:error name="series" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Country of origin</flux:label>
                            <flux:input wire:model.live="country_of_origin" placeholder="Optional" />
                            <flux:error name="country_of_origin" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 2) Gun identification --}}

            {{-- 3) Classification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Classification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Platform</flux:label>
                            <flux:select wire:model.live="platform">
                                <option value="">— Optional —</option>
                                @foreach ($this->platformOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="platform" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Type</flux:label>
                            <flux:input wire:model.live="type" placeholder="e.g., 1911, AR-15" />
                            <flux:error name="type" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Action</flux:label>
                            <flux:input wire:model.live="action" placeholder="e.g., semi-auto, bolt, pump" />
                            <flux:error name="action" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 3) Classification --}}

            {{-- 4) Core specs --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Core specs</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <flux:field>
                            <flux:label>Caliber</flux:label>
                            <flux:input wire:model.live="caliber" placeholder="e.g., 9mm" />
                            <flux:error name="caliber" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Capacity</flux:label>
                            <flux:input type="number" wire:model.live="capacity" placeholder="e.g., 15" />
                            <flux:error name="capacity" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Barrel length</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="barrel_length"
                                placeholder="e.g., 4.02" />
                            <flux:error name="barrel_length" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Overall length</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="overall_length" />
                            <flux:error name="overall_length" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Height</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="height" />
                            <flux:error name="height" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Width</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="width" />
                            <flux:error name="width" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Weight</flux:label>
                            <flux:input type="number" step="0.001" wire:model.live="weight" />
                            <flux:error name="weight" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Weight unit</flux:label>
                            <flux:select wire:model.live="weight_unit">
                                <option value="kg">kg</option>
                                <option value="lb">lb</option>
                            </flux:select>
                            <flux:error name="weight_unit" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 4) Core specs --}}

            {{-- 5) Materials & finish --}}
            <flux:accordion.item>
                <flux:accordion.heading>Materials & finish</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Frame material</flux:label>
                            <flux:input wire:model.live="frame_material" />
                            <flux:error name="frame_material" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Slide material</flux:label>
                            <flux:input wire:model.live="slide_material" />
                            <flux:error name="slide_material" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Barrel material</flux:label>
                            <flux:input wire:model.live="barrel_material" />
                            <flux:error name="barrel_material" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Finish</flux:label>
                            <flux:input wire:model.live="finish" />
                            <flux:error name="finish" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Color</flux:label>
                            <flux:input wire:model.live="color" />
                            <flux:error name="color" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Grip type</flux:label>
                            <flux:input wire:model.live="grip_type" />
                            <flux:error name="grip_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Stock type</flux:label>
                            <flux:input wire:model.live="stock_type" />
                            <flux:error name="stock_type" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Handguard type</flux:label>
                            <flux:input wire:model.live="handguard_type" />
                            <flux:error name="handguard_type" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Rail type</flux:label>
                            <flux:select wire:model.live="rail_type">
                                <option value="">— Optional —</option>
                                <option value="picatinny">Picatinny</option>
                                <option value="mlok">M-LOK</option>
                                <option value="keymod">KeyMod</option>
                                <option value="none">None</option>
                            </flux:select>
                            <flux:error name="rail_type" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 5) Materials & finish --}}

            {{-- 6) Sights & optics --}}
            <flux:accordion.item>
                <flux:accordion.heading>Sights & optics</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Sight type</flux:label>
                            <flux:input wire:model.live="sight_type" />
                            <flux:error name="sight_type" />
                        </flux:field>

                        <div class="flex items-center gap-3 mt-7">
                            <flux:switch wire:model.live="optic_ready" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Optic ready</div>
                                <div class="text-xs text-zinc-500">Slide/cut ready for optics.</div>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Optic mount pattern</flux:label>
                            <flux:input wire:model.live="optic_mount_pattern" placeholder="e.g., RMR, MOS" />
                            <flux:error name="optic_mount_pattern" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 6) Sights & optics --}}

            {{-- 7) Barrel & muzzle --}}
            <flux:accordion.item>
                <flux:accordion.heading>Barrel & muzzle</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="threaded_barrel" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Threaded barrel</div>
                                <div class="text-xs text-zinc-500">Barrel has threads.</div>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Thread pitch</flux:label>
                            <flux:input wire:model.live="thread_pitch" placeholder="e.g., 1/2x28" />
                            <flux:error name="thread_pitch" />
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="muzzle_device_included" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Muzzle device included</div>
                                <div class="text-xs text-zinc-500">Comp/flash hider included.</div>
                            </div>
                        </div>

                        <flux:field class="md:col-span-3">
                            <flux:label>Muzzle device type</flux:label>
                            <flux:input wire:model.live="muzzle_device_type"
                                placeholder="e.g., compensator, flash hider" />
                            <flux:error name="muzzle_device_type" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 7) Barrel & muzzle --}}

            {{-- 8) Safety & trigger --}}
            <flux:accordion.item>
                <flux:accordion.heading>Safety & trigger</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <flux:field class="md:col-span-2">
                            <flux:label>Trigger type</flux:label>
                            <flux:input wire:model.live="trigger_type" />
                            <flux:error name="trigger_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Trigger pull</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="trigger_pull" />
                            <flux:error name="trigger_pull" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Unit</flux:label>
                            <flux:select wire:model.live="trigger_pull_unit">
                                <option value="lb">lb</option>
                                <option value="kg">kg</option>
                            </flux:select>
                            <flux:error name="trigger_pull_unit" />
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="has_manual_safety" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Manual safety</div>
                                <div class="text-xs text-zinc-500">Has a manual safety.</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="has_firing_pin_safety" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Firing pin safety</div>
                                <div class="text-xs text-zinc-500">Has firing pin safety.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 8) Safety & trigger --}}

            {{-- 9) Metadata & inclusions --}}
            <flux:accordion.item>
                <flux:accordion.heading>Metadata & inclusions</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                        <flux:field>
                            <flux:label>Gun condition</flux:label>
                            <flux:select wire:model.live="gun_condition">
                                <option value="">— Optional —</option>
                                <option value="new">New</option>
                                <option value="like_new">Like New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                                <option value="for_parts">For Parts</option>
                            </flux:select>
                            <flux:error name="gun_condition" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Round count estimate</flux:label>
                            <flux:input type="number" wire:model.live="round_count_estimate" />
                            <flux:error name="round_count_estimate" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Included magazines</flux:label>
                            <flux:input type="number" wire:model.live="included_magazines" />
                            <flux:error name="included_magazines" />
                        </flux:field>

                        <div class="flex flex-wrap items-center gap-4 md:col-span-3">
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_box" />
                                <span class="text-sm">Has box</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_receipt" />
                                <span class="text-sm">Has receipt</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_documents" />
                                <span class="text-sm">Has documents (flag only)</span>
                            </label>
                        </div>

                        <flux:field class="md:col-span-3">
                            <flux:label>Document notes</flux:label>
                            <flux:textarea wire:model.live="document_notes" rows="3" />
                            <flux:error name="document_notes" />
                        </flux:field>

                        <flux:field class="md:col-span-3">
                            <flux:label>Included accessories</flux:label>
                            <flux:textarea wire:model.live="included_accessories" rows="3"
                                placeholder="case, sling, optic, etc." />
                            <flux:error name="included_accessories" />
                        </flux:field>

                        <flux:field class="md:col-span-3">
                            <flux:label>Extra notes</flux:label>
                            <flux:textarea wire:model.live="notes" rows="3" />
                            <flux:error name="notes" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 9) Metadata & inclusions --}}

        </flux:accordion>

        <div class="flex items-center justify-end gap-3">
            <flux:button type="submit" variant="primary">Save changes</flux:button>

            <flux:button
                href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
                variant="ghost">← Back </flux:button>
        </div>
    </form>
</div>
